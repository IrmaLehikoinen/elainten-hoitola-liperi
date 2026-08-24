# Novi — tekninen katsaus koodikatselmusta varten

Tämä dokumentti on tarkoitettu ulkopuoliselle tekniselle asiantuntijalle, joka tekee koodikatselmuksen "novi"-järjestelmään. Järjestelmä on lemmikkihoitola-alan ajanvaraus- ja asiakashallintajärjestelmä, joka on rakennettu niin, että sama pohja voidaan myöhemmin myydä myös muille toimialoille (esim. parturi, hierontapiste) ilman että pohjaa tarvitsee muuttaa.

Päivätty: 24.8.2026

---

## 1. Tekninen perusta

Järjestelmä on rakennettu Laravel-sovelluskehyksellä (versio ^13.8, PHP ^8.3). Laravel on yksi käytetyimmistä PHP-sovelluskehyksistä ammattimaisessa web-kehityksessä, ja sillä on valmiit, hyvin testatut ratkaisut moneen turvallisuuskriittiseen asiaan (CSRF-suojaus, SQL-injektion esto parametrisoiduilla kyselyillä, salasanojen hajautus, istuntojen hallinta, oikeuksien tarkistus, syötteen validointi). Tämä tarkoittaa, ettei perusturvallisuutta ole rakennettu tyhjästä — se nojaa sovelluskehyksen valmiisiin, laajasti auditoituihin mekanismeihin, joita sovelletaan johdonmukaisesti koko koodikannassa.

Myös järjestelmän tuleva julkinen, brändätty etusivu (verkkosivun "kotisivu-osuus", ei vielä toteutettu — ks. kohta 9) rakennetaan samaan Laravel-sovellukseen, ei erilliseen työkaluun. Tämä pitää koko asiakaspolun (markkinointisivu → ajanvarauslaatikko → hallintapaneeli) yhden koodikannan ja yhden tietoturvamallin alla.

---

## 2. Upotus asiakkaan omalle verkkosivulle

Ajanvarauslaatikko (`resources/views/modules/lemmikkihoitola/components/booking-widget.blade.php`) on tietoisesti eriytetty omaksi Blade-komponentikseen, jonka kaikki CSS-luokat on nimiavaruutettu etuliitteellä `.novi-booking-widget` (ks. commit `7dca057`). Tämä on tehty juuri upotusta varten: kun laatikko myöhemmin upotetaan asiakkaan omalle verkkosivulle (tavallinen PHP-toteutettu sivu tai WordPress), sen tyylit eivät voi törmätä isäntäsivuston omiin CSS-luokkiin, eikä isäntäsivuston tyylit pääse rikkomaan laatikon ulkoasua.

Valittu upotustapa on **iframe-upotus**: asiakkaan sivustolle lisätään `<iframe>`-elementti, joka osoittaa suoraan noviin isännöityyn `/varaa`-reittiin (`PublicBookingController::start()`). Tämä on tarkoituksella yksinkertaisin ja turvallisin tapa:

- Ei vaadi mitään asiakkaan CMS:ään asennettavaa (toimii yhtä lailla käsin koodatulla PHP-sivulla kuin WordPressissä — WordPressissä riittää HTML/iframe-lohko tai vastaava lisäosa).
- Koko lomakelogiikka, validointi, istunto ja maksukytkentä pysyvät kokonaan novin palvelimella; isäntäsivusto ei koskaan käsittele asiakkaan syöttämiä henkilö- tai maksutietoja.
- Ei JS-riippuvuuksia tai skriptikonflikteja isäntäsivun kanssa (vrt. JS-upotettava widjetti, joka jakaisi suoritusympäristön isäntäsivun oman JS:n kanssa).
- Domain-rajat pysyvät selkeinä: cross-site-riskit (esim. isäntäsivun XSS ei pääse käsiksi novin istuntoon) rajoittuvat iframe-sandboxin sisään.

Haittapuolena iframe vaatii koon säädön (responsiivisuus) ja ei periytä isäntäsivun typografiaa automaattisesti — tätä varten laatikko on tyylitelty visuaalisesti neutraaliksi ja brändättäväksi (`BrandManager`, ks. kohta 8).

---

## 3. Koodin kolmikerroksinen rakenne: pohja / moduuli / yrityksen omat asetukset

Koodi on jaettu kolmeen kerrokseen, joilla kullakin on tarkka vastuu:

**Pohja** (`app/Models/*.php` root-tasolla, `app/Services/AvailabilityService.php`, `app/Services/StripeCheckoutService.php`, `app/Http/Controllers/StripeWebhookController.php`, `app/Events/StripeCheckoutCompleted.php`, `app/Http/Middleware/*`, `app/Core/Branding/BrandManager.php`, `routes/web.php`, `resources/views/partials/emails/*`) sisältää vain toimialasta riippumatonta logiikkaa: kapasiteettilaskenta, Stripe-maksut, brändäys, monivuokralaisuuden (multi-tenancy) rajaus, sähköpostien yhteinen ulkoasu. Pohja ei tunne käsitteitä "varaus", "lemmikki" tai "asiakas" — se tuntee vain geneerisiä käsitteitä kuten "resurssi", "kapasiteetti" ja "maksu".

**Toimialamoduuli** (`app/Modules/Lemmikkihoitola/**`, näkymät `resources/views/modules/lemmikkihoitola/**`) sisältää kaiken lemmikkihoitola-spesifin: `Booking`, `Pet`, `Customer`-mallit, ajanvarauksen kontrollerit, sähköpostisisällöt, oman reititystiedoston ja oman asetuskonfiguraationsa (`public_booking_fields.php`).

**Yrityksen omat säädöt** eivät ole koodia ollenkaan, vaan tietokantarivejä: `Company.settings` (JSON-sarake, esim. `deposit_percentage`, `base_daily_rate`) ja `Company.industry`-kenttä joka kertoo, mikä toimialamoduuli on käytössä. Näin sama koodi palvelee eri yrityksiä eri asetuksilla ilman koodihaarautumista per asiakas.

### Miksi tämä mahdollistaa uuden toimialamoduulin lisäämisen pohjaa muokkaamatta

Moduuli liittyy pohjaan **yhden ainoan tiedoston** kautta: `LemmikkihoitolaServiceProvider.php` (Laravelin service provider -mekanismi). Tämä tiedosto on ainoa paikka, joka "tietää" sekä pohjasta että moduulista — pohja itse ei koskaan importtaa mitään moduulin luokkaa. Kytkentä tapahtuu neljällä Laravelin standardimekanismilla:

1. **Riippuvuusinjektio suljinfunktiolla (closure).** Pohjan `AvailabilityService` ei osaa itse laskea "kuinka moni on jo vahvistetusti varattu" — tämä riippuu aina toimialan omasta varauskäsitteestä. Moduuli antaa tämän tiedon `$this->app->bind(AvailabilityService::class, fn() => new AvailabilityService($closure))`-kutsulla `register()`-metodissa. Uusi toimiala (esim. parturi) kirjoittaisi tähän oman closure-toteutuksensa, joka laskee varatun kapasiteetin `Appointment`-malliltaan — `AvailabilityService`-luokkaa itseään ei tarvitse koskea.
2. **Laravel-eventit.** Pohjan Stripe-webhook ei tiedä mitä maksun onnistumisen jälkeen pitää tapahtua — se vain julkaisee `StripeCheckoutCompleted`-eventin. Moduuli kuuntelee sitä `Event::listen(...)`-kutsulla `boot()`-metodissa ja päättää itse, mitä tehdään (varauksen vahvistus, vahvistussähköposti). Uusi toimiala kirjoittaisi oman kuuntelijansa.
3. **`loadRoutesFrom()` ja `View::addLocation()`.** Moduuli lataa omat reittinsä (`routes.php`) ja näkymäkansionsa itse `boot()`-metodissa. Pohjan `routes/web.php` pysyy täysin tyhjänä moduulikohtaisista reiteistä.
4. **`mergeConfigFrom()` ja `Config::set()`.** Moduulin oma asetustiedosto (`public_booking_fields.php`) ja sivuvalikon linkit rekisteröidään moduulin omasta `ServiceProvider`ista, ei kovakoodattuna pohjaan.

Käytännössä: uuden toimialan lisäys tarkoittaa uutta `app/Modules/<Toimiala>/`-kansiota + yhtä `ServiceProvider`ia, joka rekisteröidään `config/app.php`:ssa. Pohjan tiedostoihin ei kosketa kertaakaan. `LemmikkihoitolaServiceProvider.php`:n alussa on tätä varten tarkoituksella jätetty "MALLIESIMERKKI"-kommentti, joka ohjeistaa kopioimaan tiedoston rakenteen (ei sisältöä) uudelle moduulille. Vastaavat lyhyet malliesimerkki-kommentit on lisätty myös tiedostoihin `Booking.php`, `PaymentController.php` ja `routes.php`.

---

## 4. Versiointi

Pohja ja moduuli on versioitu **erikseen**, koska molempia päivitetään eri tahtiin useille asiakkaille: `config/versions.php` sisältää avaimet `pohja` ja `lemmikkihoitola_moduuli`, kumpikin nyt `1.0.0`. Lisäksi git-historiassa on erilliset tagit `pohja-1.0.0` ja `lemmikkihoitola-moduuli-1.0.0`. Käytäntönä on: kun vain moduulia muutetaan (esim. lemmikkihoitola-spesifi ominaisuus), pohjan versionumero pysyy ennallaan, ja päinvastoin. Tämä mahdollistaa myöhemmin tilanteen, jossa esim. kaikki asiakkaat saavat pohjapäivityksen 1.1.0, mutta vain osa saa moduulipäivityksen — versiot voidaan seurata ja kommunikoida asiakkaille erillisinä.

---

## 5. Asiakkaan näkyvä polku: ajanvarauksen koko ketju

Julkinen ajanvarauspolku kulkee kokonaan `PublicBookingController`in (`app/Modules/Lemmikkihoitola/Http/Controllers/PublicBookingController.php`) kautta, reitit on määritelty tiedostossa `app/Modules/Lemmikkihoitola/routes.php` prefiksillä `varaa`, throttlattuna `throttle:60,1`-middlewarella.

1. **`start()` (GET `/varaa`)** — näyttää palveluvalinnan (`CareType`-lista).
2. **`availability()` (POST `/varaa/vapaat-ajat`)** — asiakas syöttää lemmikit (laji per lemmikki) ja hoidon keston. Validoidaan Laravelin `Request::validate()`-mekanismilla (pakolliset kentät, tyypit, `exists:care_types,slug` -tarkistus ettei väärennettyä palvelu-ID:tä voi lähettää). Valinnat tallennetaan **istuntoon** (`session()`), ei URL-parametreihin — tämä estää sen, että käyttäjä pääsisi muokkaamaan hintaan/kapasiteettiin vaikuttavia arvoja URL:ia näpelöimällä. `AvailabilityService::findStartDates()` laskee mahdolliset aloituspäivät käymällä läpi 365 päivän ikkunan ja tarkistamalla jokaiselle päivälle kapasiteetin riittävyyden.
3. **`hold()` (POST `/varaa/hold`)** — kun asiakas valitsee päivämäärän, luodaan `BookingHold`-rivi (10 minuutin voimassaoloajalla, `expires_at`). Tämä on **kaksoisvarauksen esto -mekanismi**: ennen holdin luontia kutsutaan uudelleen `AvailabilityService::isAvailable()`, joka laskee yhteen jo vahvistetut varaukset JA voimassa olevat holdit. Jos kaksi asiakasta yrittää varata saman viimeisen paikan samaan aikaan, vain ensimmäinen onnistuu — toinen saa virheilmoituksen kapasiteetin loppumisesta.
4. **`identify()` (POST `/varaa/tunnista`, throttlattu `throttle:5,1`)** — asiakas syöttää sähköpostin. Jos asiakas on jo järjestelmässä, lähetetään **allekirjoitettu (signed) linkki** (`URL::temporarySignedRoute`, 30 min voimassa) sähköpostiin — asiakas ei kirjaudu salasanalla, vaan linkin allekirjoitus (Laravelin HMAC-pohjainen `signed`-middleware) todistaa, että linkin haltija todella sai sen kyseiseen sähköpostiin. Tiukka throttle (5 pyyntöä/min) estää sähköpostiosoitteiden massakalasteluyritykset.
5. **`verify()` (GET `/varaa/vahvista/{customer}`, `middleware('signed')`)** — Laravel tarkistaa automaattisesti allekirjoituksen ja vanhentumisajan ennen kuin kontrolleri edes suoritetaan. Jos linkki on väärennetty tai vanhentunut, pyyntö hylätään ennen kontrollerikoodia.
6. **`store()` (POST `/varaa/tallenna`)** — lopullinen tallennus. Tässä tapahtuu useita suojauksia samanaikaisesti:
   - Kaikki kentät validoidaan uudelleen palvelimella (asiakkaan selaimessa tehty validointi ei koskaan riitä yksinään).
   - **Uudelleentarkistus (recheck) ennen tallennusta**: oma hold vapautetaan ja `AvailabilityService::isAvailable()` ajetaan vielä kerran juuri ennen `Booking::create()`-kutsua. Tämä on niin sanottu "optimistinen lukitus + uudelleentarkistus" -kuvio, joka sulkee pois race condition -tilanteen, jossa kaksi pyyntöä ehtisi molemmat läpi holdin ja lopullisen tallennuksen välissä.
   - **IDOR-suojaus (Insecure Direct Object Reference) lemmikkikortin päivityksessä**: kun asiakas päivittää olemassa olevaa lemmikkiä (`pet_id` lähetetään lomakkeella), haku tehdään aina `Pet::where('customer_id', $customer->id)->find($petData['pet_id'])` — eli asiakas ei voi lähettää toisen asiakkaan lemmikin ID:tä ja päästä muokkaamaan sitä (ks. commit `c4aff25`, "pet_id-omistajuustarkistus").
   - Onnistuneen tallennuksen jälkeen istunnon varaustiedot tyhjennetään (`session()->forget('public_booking')`), jottei sama data jää selattavaksi/uudelleenlähetettäväksi.
   - Jos varaus vaatii ennakkomaksun, käyttäjä ohjataan `PaymentController::checkout()`-reitille, joka rakentaa Stripe-checkout-session generisen `StripeCheckoutService`in kautta (30 min maksuaika, `preferred_deadline`-parametrilla).

**Asiakas- ja lemmikkikortin tallennus paneeliin**: `store()`-metodissa uusi tai olemassa oleva `Customer` päivitetään/luodaan, jokaiselle lemmikille luodaan tai päivitetään `Pet`-rivi, ja jokaiselle lemmikille luodaan `BookingParticipant`-rivi joka sitoo lemmikin varaukseen tietyllä hoitojaksolla. Kaikki nämä ilmestyvät suoraan hallintapaneeliin (Asiakkaat-, Kalenteri- ja Varaukset-näkymiin) ilman erillistä hyväksyntävaihetta — hoitajan tehtäväksi jää kuittaus (`acknowledge`-toiminto).

---

## 6. Hallintapaneeli kokonaisuutena

Kaikki hallintapaneelin reitit (`app/Modules/Lemmikkihoitola/routes.php`, prefiksi `admin/*` sekä `/dashboard`, `/calendar`) ovat suojattu `middleware(['auth', 'verified'])`-yhdistelmällä — kirjautuminen ja sähköpostin vahvistus pakollisia. Paneeli koostuu seuraavista kokonaisuuksista, kukin oma kontrollerinsa:

- **Etusivu/Dashboard** (`DashboardController`) — päivän tilannekuva.
- **Kalenteri** (`CalendarController`, `CalendarCapacityController`) — päivänäkymä, kapasiteettipoikkeukset tietylle päivälle (`DateCapacityOverride`).
- **Varaukset** (`AdminBookingController`) — varausten listaus, luonti suoraan paneelista (sama kapasiteettitarkistus ja hold-mekanismi kuin julkisessa lomakkeessa), peruutus, ennakkomaksun manuaalinen merkkaus, hoitojakson muokkaus per lemmikki.
- **Asiakkaat** (`CustomerController`) — asiakaslista, asiakaskortti, tietosuojatoiminnot (ks. kohta 7).
- **Lemmikit** (`PetController`) — lemmikkikortin muokkaus.
- **Muistutukset** (`ReminderController`).
- **Palvelut** (`ServiceSelectionController`).
- **Laskutus** (`InvoiceController`) — kuittien/laskujen luonti, PDF-lataus (`Barryvdh\DomPDF`), ohitus.
- **Raportit** (`ReportController`).
- **Yritysasetukset** (`CompanySettingsController`) — ennakkomaksuprosentti, päivähinta, resurssit (kapasiteetti per laji), palvelut, muistutustyypit, hoitotyypit, mitkä ajanvarauslomakkeen kentät ovat käytössä.

Jokainen näistä malleista käyttää `BelongsToCompany`-traitia (ks. kohta 8) — sama paneelikoodi palvelee useaa yritystä ilman että ne näkevät toistensa dataa.

---

## 7. Tietosuoja (GDPR)

- **Tietosuojaseloste**: julkinen reitti `/tietosuoja` (määritelty pohjan `routes/web.php`:ssä, koska sisältö on yleinen eikä toimialasidonnainen), näyttää `Company`-mallin tiedot.
- **Tarkastusoikeus (GDPR art. 15)**: `CustomerController::dataExport()` kokoaa kaiken asiakkaasta tallennetun tiedon (lemmikit, varaukset, osallistujarivit, laskut) yhdelle sivulle. `dataExportPdf()` tuottaa saman PDF-muodossa (`Barryvdh\DomPDF`) ladattavaksi tai asiakkaalle toimitettavaksi.
- **Oikeus tulla unohdetuksi**: `CustomerController::destroy()` kutsuu `Customer::eraseForPrivacy()`-metodia. Logiikka: jos asiakkaalla on laskuja, henkilötiedot (nimi, sähköposti, puhelin, osoite, muistiinpanot) korvataan/tyhjennetään ja lemmikit poistetaan, mutta varaus- ja laskuhistoria säilyy anonymisoituna — tämä on tarkoituksellinen kompromissi, koska Suomen kirjanpitolaki edellyttää tositteiden (laskujen) säilyttämistä tietyn ajan, vaikka henkilö pyytäisi tietojensa poistoa. Jos laskuja ei ole, asiakas poistetaan kokonaan tietokannasta.

---

## 8. Tietoturva

Kooste tietoturvamekanismeista, joita katselmuksessa kannattaa tarkistaa nimenomaan alla mainituista kohdista:

- **Monivuokralaisuuden (multi-tenancy) rajaus** — `app/Models/Concerns/BelongsToCompany.php`. Trait lisää jokaiseen sitä käyttävään malliin globaalin Eloquent-scopen (`addGlobalScope`), joka rajaa jokaisen kyselyn automaattisesti kirjautuneen käyttäjän `company_id`:hen, ja täyttää `company_id`:n automaattisesti uutta riviä luotaessa (`creating`-hook). Tämä on koko järjestelmän tärkein tietoturvamekanismi moniyritysympäristössä: yksittäisen kontrollerin ei tarvitse (eikä pidäkään) itse muistaa lisätä `where('company_id', ...)`-ehtoa — se tapahtuu mallitasolla automaattisesti, mikä poistaa inhimillisen unohdusvirheen riskin. Huomio katselmoijalle: traitia ei ole tarkoituksella liitetty `User`-malliin (kommentoitu syy suoraan koodissa: kanan ja munan -ongelma kirjautumisessa).
- **CSRF-suojaus** — Laravelin oletus on päällä kaikilla lomakkeilla/POST-pyynnöillä. Ainoa poikkeus on `stripe/webhook`-reitti (`bootstrap/app.php`: `validateCsrfTokens(except: ['stripe/webhook'])`), mikä on välttämätöntä koska Stripe kutsuu tätä reittiä palvelimelta palvelimelle ilman selainistuntoa/CSRF-tokenia. Tämä poikkeus on turvallinen, koska `StripeWebhookController` varmistaa pyynnön aitouden Stripen omalla allekirjoitustarkistuksella (webhook-salaisuus) ennen kuin mitään dataa käsitellään.
- **Signed URLs** — käytetään sekä asiakkaan taikalinkki-kirjautumisessa (`temporarySignedRoute`, 30 min) että voitaisiin käyttää vastaavasti muissa ajastetuissa/kertakäyttöisissä linkeissä. HMAC-pohjainen allekirjoitus estää linkin väärentämisen tai voimassaoloajan pidentämisen ilman palvelimen salaisuutta (`APP_KEY`).
- **Rate limiting (throttle)** — julkiset, resursseja kuluttavat tai kalastelulle alttiit reitit on rajoitettu: `/varaa/*` 60 pyyntöä/min, `/varaa/tunnista` (sähköpostikysely) 5 pyyntöä/min, `/varaukset/{booking}/maksa` 30 pyyntöä/min.
- **Julkinen rekisteröityminen on poistettu tietoisesti** (ks. commit `c4aff25`) — hallintapaneeliin ei pääse luomaan tunnusta itse kuka tahansa; käyttäjätilit luodaan hallitusti. Tämä pienentää hyökkäyspintaa merkittävästi verrattuna oletus-Breeze-skaffoldiin, jossa rekisteröityminen on oletuksena auki.
- **Kuittien/laskujen suojaus** — `invoices.show` ja `invoices.pdf`-reitit vaativat `auth`+`verified`-middlewaren; asiakas ei pääse toisen asiakkaan laskuun suoraan URL:ia arvaamalla ilman kirjautumista paneeliin (ja `BelongsToCompany`-scope estää pääsyn toisen yrityksen dataan vaikka kirjautuneena).
- **IDOR-suojaus lemmikkitiedoissa** — ks. kohta 5, `store()`-metodin `pet_id`-omistajuustarkistus.
- **Kaksoisvarauksen/race condition -esto** — `BookingHold` + uudelleentarkistus juuri ennen tallennusta (ks. kohta 5). Tämä on syytä testata katselmuksessa nimenomaan samanaikaisilla pyynnöillä, koska se on koko järjestelmän herkin samanaikaisuusongelma.
- **Syötteen validointi** — jokainen kirjoittava kontrollerimetodi käyttää Laravelin `Request::validate()`-mekanismia eksplisiittisillä säännöillä (tyyppi, pituusrajat, `exists:`-tarkistukset viiteavaimille). Ei yhtään kontrolleria, joka ottaisi raakaa `$request->all()`-syötettä suoraan tietokantaan.
- **Salasanat** — Laravel Breezen (`laravel/breeze ^2.4`) oletustoteutus, joka käyttää bcrypt-hajautusta Laravelin `Hash`-facadea kautta. Ei itse kirjoitettua salasanakäsittelyä.
- **Salaisuuksien hallinta** — Stripe-avaimet, tietokantatunnukset ja `APP_KEY` ovat `.env`-tiedostossa, joka on `.gitignore`:ssa (ei koskaan committoitu versionhallintaan). `APP_KEY` on asetettu.
- **Istuntoturvallisuus** — `config/session.php` tukee `SESSION_SECURE_COOKIE`, `SESSION_HTTP_ONLY` (oletus `true`) ja `SESSION_SAME_SITE` (oletus `lax`) -ympäristömuuttujia. **Huomio katselmoijalle**: `SESSION_SECURE_COOKIE` ei ole vielä pakotettu `true`:ksi `.env`:ssä — tuotantoon vietäessä (HTTPS käytössä) tämä kannattaa eksplisiittisesti asettaa, jotta istuntoeväste ei koskaan kulje salaamattomana.
- **Ei kenttätason salausta levossa (encryption at rest)** — arkaluontoiset kentät (esim. lemmikin terveystiedot, allergiat, lääkitys) tallennetaan tietokantaan selkotekstinä, ei Laravelin `encrypted`-castilla. Suoja perustuu pääsynhallintaan (kirjautuminen + `BelongsToCompany`-rajaus), ei salaukseen. Tämä on standardikäytäntö vastaavissa SaaS-järjestelmissä, mutta on syytä mainita katselmoijalle eksplisiittisesti, koska osa datasta on terveystietoihin rinnastettavaa.

---

## 9. Mistä mikäkin löytyy koodista (tiedostokartta katselmusta varten)

**Pohja (`novi/`):**
- `app/Models/Resource.php`, `BookingHold.php`, `DateCapacityOverride.php` — geneeriset kapasiteettimallit.
- `app/Services/AvailabilityService.php` — kapasiteettimoottori, closure-injektio, ks. kohta 3.
- `app/Services/StripeCheckoutService.php` — geneerinen Stripe-checkout-session-luonti.
- `app/Http/Controllers/StripeWebhookController.php` + `app/Events/StripeCheckoutCompleted.php` — webhookin allekirjoitustarkistus ja eventin julkaisu.
- `app/Http/Middleware/ShareCompanyBranding.php` + `app/Core/Branding/BrandManager.php` — brändäystietojen jako näkymiin (myös sähköposteissa, HTTP-pyynnöstä riippumatta).
- `app/Models/Concerns/BelongsToCompany.php` — monivuokralaisuuden globaali scope, ks. kohta 8.
- `routes/web.php` — pohjan omat reitit (etusivu, profiili, tietosuojaseloste, Stripe-webhook).
- `resources/views/partials/emails/header.blade.php`, `footer.blade.php` — sähköpostien yhteinen kehys.
- `config/versions.php` — pohjan ja moduulin versionumerot.
- `bootstrap/app.php` — middleware-rekisteröinti, CSRF-poikkeus.

**Lemmikkihoitola-moduuli (`novi/app/Modules/Lemmikkihoitola/`):**
- `LemmikkihoitolaServiceProvider.php` — **aloita katselmus tästä tiedostosta**: se on koko moduulin liitäntäpiste pohjaan, ja sisältää malliesimerkkikommentin joka selittää rakenteen.
- `Http/Controllers/PublicBookingController.php` — koko julkinen varauspolku (kohta 5).
- `Http/Controllers/AdminBookingController.php`, `CalendarController.php`, `CalendarCapacityController.php`, `CustomerController.php`, `PetController.php`, `InvoiceController.php`, `ReportController.php`, `CompanySettingsController.php`, `DashboardController.php`, `PaymentController.php`, `ServiceSelectionController.php`, `ReminderController.php`, `BookingHoldController.php` — hallintapaneeli (kohta 6).
- `Models/Booking.php`, `Customer.php`, `Pet.php`, `BookingParticipant.php`, `Invoice.php` ym. — toimialan omat mallit.
- `Mail/*.php` — sähköpostimallipohjat (varausvahvistus, maksukehotus, taikalinkki, maksun vanheneminen).
- `Console/Commands/CancelExpiredBookings.php` — ajastettu komento vanhentuneiden varausten peruutukseen.
- `config/public_booking_fields.php` — julkisen lomakkeen kenttäasetukset.
- `routes.php` — kaikki moduulin reitit, ladataan `ServiceProvider`ista.
- `resources/views/modules/lemmikkihoitola/**` — kaikki moduulin näkymät, mukaan lukien `components/booking-widget.blade.php` (upotettava laatikko).

**Tietosuoja:** `CustomerController::dataExport()`, `dataExportPdf()`, `destroy()` + `Customer::eraseForPrivacy()` (kohta 7).

**Maksut:** `PaymentController::checkout()` → `StripeCheckoutService::createSessionUrl()` → Stripe-checkout → `StripeWebhookController::handle()` → `StripeCheckoutCompleted`-event → kuuntelija `LemmikkihoitolaServiceProvider::boot()`:ssa, joka vahvistaa varauksen ja lähettää vahvistussähköpostin.

---

## 10. Yhteenveto katselmoijalle

Suosittelen katselmuksen aloitusjärjestykseksi: (1) `LemmikkihoitolaServiceProvider.php` kokonaiskuvan saamiseksi pohja/moduuli-rajapinnasta, (2) `BelongsToCompany.php` monivuokralaisuuden ymmärtämiseksi, (3) `PublicBookingController.php` + `AvailabilityService.php` koko julkisen polun ja kaksoisvarauksen eston läpikäymiseksi, (4) `StripeWebhookController.php` + `StripeCheckoutCompleted`-event maksuvirran läpikäymiseksi, (5) `CustomerController.php`:n tietosuojametodit.

Kysymyksiin vastaan mielelläni — Irma Lehikoinen, irma.lehikoi@gmail.com.
