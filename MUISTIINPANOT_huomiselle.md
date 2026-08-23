# Muistiinpanot – jatketaan huomenna (kirjoitettu 15.8.2026)

## 1. Yhteistyötapa (tämä pätee jatkossakin, ei vain huomenna)

- Tehdään yksi tehtävä kerrallaan, ei useaa asiaa samaan aikaan.
- Claude ei koskaan muokkaa `novi`-kansion tiedostoja suoraan. Kaikki koodimuutokset annetaan aina valmiina "Poistettava alue" / "Mitä liitetään tilalle" -pareina (tai koko tiedoston korvauksena, jos osittainen muutos on liian riskialtis).
- Irma kopioi koodin itse VS Codeen, tallentaa, ja testaa/tarkistaa lopputuloksen (selaimessa ja/tai terminaalissa, esim. `npm run build`, Laravel-lokit).
- Kun muutos on testattu ja toimii, Irma vastaa "tehty" (tai kertoo jos ei toiminut), ja vasta sitten siirrytään seuraavaan tehtävään.
- Claude ei kysy AskUserQuestion-kysymyskenttiä – kysymykset kirjoitetaan tavallisena tekstinä chattiin.

## 2. Joka aamu ensimmäisenä: miten avaan tämän päivän kehitysympäristön

1. Avaa **VS Code** ja avaa siihen `novi`-kansio (koko projekti).
2. Avaa VS Codessa terminaali ja käynnistä Laravelin oma palvelin:
   ```
   cd novi
   php artisan serve
   ```
   Terminaali näyttää osoitteen, esim. `http://127.0.0.1:8000` (tai vastaava portti).
3. Kopioi terminaalissa näkyvä osoite selaimeen ja avaa se.
4. Kirjaudu sisään hallintapaneeliin omilla Laravel-tunnuksilla.
5. Jos edellisenä päivänä tehtiin muutoksia Tailwind/CSS-luokkiin, aja terminaalissa (eri terminaali-ikkunassa, koska `php artisan serve` jää pyörimään):
   ```
   cd novi
   npm run build
   ```
6. Jos jokin sivu antaa virheen, tarkista Laravelin loki:
   ```
   tail -n 50 novi/storage/logs/laravel.log
   ```
7. Kerro Claudelle lyhyesti mistä jatketaan (tai viittaa tähän muistiinpanoon).

## 3. Mitä jäi tänään kesken / tekemättä hallintapanelissa

### Kalenteri-välilehti (ei etusivun kalenteri, oma erillinen toteutus)
- Kalenteri-välilehden kalenteri **ei ole sama** kuin etusivun kalenteri – tarvitsee oman toteutuksen/omat toiminnot.
- Kun klikkaa kalenterista jotain varausta/päivää, pitää näkyä:
  - mitkä eläimet ovat hoidossa sinä päivänä,
  - mitkä niistä **lähtevät** pois sinä päivänä,
  - mitkä **saapuvat** sinä päivänä,
  - onko näille eläimille muistutuksia kyseiselle päivälle.
- Ylhäällä oleva "takaisin"-nuoli palauttaa tällä hetkellä virheellisesti etusivulle – sen pitää palauttaa **kalenterisivulle**.

### Varaukset-välilehti
- Pitää näyttää kaikki varaukset – sama ajanvarauskortti/-näkymä kuin mikä syntyy varausta tehdessä.
- Miten varaukset jaotellaan (esim. tulevat/menneet/tyypeittäin) – **mietitään huomenna**.

### Asiakkaat-välilehti
- Tämänhetkinen toteutus on hyvä sellaisenaan, ei muutostarpeita juuri nyt.

### Palvelut-välilehti
- Pitää listata kaikki mahdolliset palvelut, joita koirahoitoloilla voi olla tarjolla.
- Palveluiden pitää olla muokattavissa samaan tapaan kuin asiakastiedot (CRUD-näkymä).
- Eläinkortilta pitää voida valita/tallentaa hoidon aikana käytetyt palvelut, jotka tulostuvat kuitiksi hoidon päättyessä.
- **Tutkitaan/suunnitellaan huomenna.**

### Laskutus-välilehti
- Kuitin tulostus, kun koira lähtee hoidosta.
- Joillekin asiakkaille mahdollisuus lähettää lasku sähköpostitse (ei kaikille pakollinen ominaisuus).

### Raportit-välilehti
- Sisältö täysin auki – mitä raportteja tarvitaan? **Mietitään huomenna.**

## 4. Muuta avointa (aiemmista listoista, ei unohdeta)
- Tehtävä #10: Kuitti hoidon päättyessä (liittyy Laskutus-osioon yllä).
- Tehtävä #21: Etusivu – muistutukset ja saapumiset/lähdöt eroteltu (tehty tänään), tarkista vielä että "rasti poistaa rivin" toimii myös saapumiset/lähdöt-listassa (nyt toteutettu vain muistutuksille).
- Tehtävä #23: Päivän commit – tehtiin tänään VS Codesta.
- Tehtävä #24 (kesken): Kalenterin varauslomake – puhelinnumeron muotoilu, paluu etusivulle, selattava kalenteri, uuden lemmikin lisäys.
- Väriohjaus kalenterissa (yrittäjän varaukset vihreä / asiakkaan ruskea + "päivä täynnä" -merkintä pinkillä) – tietoisesti lykätty, ei aloitettu. Vaatii uuden `source`-sarakkeen `bookings`-tauluun.

## 5. Muistiinpanot 16.8.2026 – ISO PÄIVITYS

### Tehty tänään (16.8.)
- Koko Laskutus/kuitti-ominaisuus valmiiksi: PDF-kuitit ja -laskut (dompdf), ALV eriteltynä, suomalainen viitenumero (mod10), eräpäivä (maksuehto asetuksista, oletus 14 pv), Saaja/Maksaja-maksutietolohko, kuitti ja lasku samasta varauksesta kahtena erillisenä tulosteena (kuitissa ei eräpäivää, laskussa on).
- Palvelut-sivu ja Raportit-sivu valmiit (haku, hoitokerrat, käytetyt palvelut).
- Yritystiedot-välilehti asetuksiin: virallinen nimi, puhelin, Y-tunnus, osoite, IBAN, maksuehto (pv netto), ALV-prosentti, ja UUSIMPANA: Pääväri + Toissijainen väri (hex-kentät, synkassa väriruudun kanssa) – nämä ohjaavat VAIN kuittien/laskujen värejä, eivät hallintapaneelin ulkoasua.
- Kaikki committoitu ja pushattu GitHubiin.

### TÄRKEÄ PÄÄTÖS: ennen kuin mitään muuta tehdään, nämä kolme korjataan ensin (Irman oma järjestys, 16.8.)
1. **Etusivu:** muistutukset ja saapumiset/lähdöt pitää erotella omiksi osioikseen, ja rasti-toiminto rivin poistoon (koskee myös saapumiset/lähdöt-listaa, ei vain muistutuksia – ks. kohta 4 tehtävä #21 yllä).
2. **Kalenterin varauslomake:** puhelinnumeron muotoilu, paluu etusivulle (korjaa oikeaan kohteeseen), selattava kalenteri, uuden lemmikin lisäys – kesken (tehtävä #24).
3. **Hoitojakson pidennys/lyhennys kesken hoidon:** hoidossa olevan eläimen hoitojakson pituutta pitää voida muuttaa kesken hoidon. Ei vielä aloitettu ollenkaan.

Irma haluaa vielä keskustella jostain ennen kuin näihinkään edes ryhdytään – tarkista chatista mitä se koski, ei kirjattu vielä tähän.

### Iso seuraava kokonaisuus (odottaa yllä olevien kolmen jälkeen)
Tavoite: julkinen verkkosivu + ajanvarausjärjestelmä samaan novi-sovellukseen (ei erillinen paketti – Laravelin `public`-kansio hoitaa jo turvallisen erottelun hostauksessa).
1. Verkkosivun etusivu – Irma päätti: pidetään tyhjänä, VAIN ajanvarauslomake sivun loppuun.
2. Asiakkaan kirjautuminen: taikalinkki sähköpostiin (EI salasanaa) – oma "customer"-guard, erillinen henkilökunnan kirjautumisesta.
3. Julkinen ajanvarauslomake – käyttää samaa AvailabilityServiceä ja BookingHold-mekanismia kuin hallintapaneeli (näin ei tule päällekkäisvarauksia), päättyy Stripe-maksuun (payment.checkout ja StripeWebhookController ovat jo olemassa ja lähettävät vahvistussähköpostin automaattisesti onnistuneesta maksusta).
4. Kalenteriin uusien nettivarausten erottelu: vaaleanpunainen kunnes yrittäjä avaa/klikkaa varauksen, sitten vihreäksi. Plus pieni "X uutta varausta" -laskuri kalenterin päälle. Vaatii uuden `acknowledged_at`-kentän Bookingiin + `confirmation_channel`-kenttä on jo olemassa (arvo esim. "online" vs "admin").

### Myöhempi, tietoisesti lykätty: tuotteistus toiselle asiakkaalle
- Irman bisnesmalli: jokainen asiakas ostaa OMAN, täysin erillisen kopion koko järjestelmästä + oman tietokannan. Ei monivuokralainen SaaS, ei yhteyttä asiakkaiden välillä.
- Koodikanta on jo pitkälti tähän valmis (lähes kaikki yritystieto on tietokantapohjaista per `company_id`, ei kovakoodattua).
- Kun aika on kypsä: kloonaa git-repo uuteen kansioon/repoon, perusta tyhjä tietokanta, aja migraatiot, asiakas täyttää itse Yritystiedot/eläinryhmät/palvelut/hinnat/brändivärit samojen asetussivujen kautta – ei koodimuutoksia.
- Hallintapaneelin oma ulkoasu (`project/brand.php`, novi-kansion ulkopuolella) pysyy AINA samana kaikilla asiakkailla – se on Irman oma tuotebrändi, ei muuteta per asiakas.
- Ei aloiteta tätä ennen kuin Irman oma järjestelmä on muuten täysin valmis ja käytössä.

### Kansiorakennepäätös (16.8., myöhäisilta) – LOPULLINEN PÄÄTÖS, korvaa aiemmat pohdinnat

Käytiin läpi montaa vaihtoehtoa (erillinen `public`-kansio, iframe, alidomainit). Lopullinen päätös ensimmäistä pilottiasiakasta varten:

**PILOTTIASIAKAS (ei vielä olemassa olevaa nettisivua) → EI iframea, EI erillistä public-kansiota.**
- Koko paketti — nettisivun etusivu, julkinen ajanvarauslomake JA hallintapaneeli — rakennetaan YHTENÄ Laravel-sovelluksena (`novi`), yhteen tietokantaan.
- Julkiset sivut (etusivu `/`, ajanvarauslomake esim. `/varaa`) tulevat tavallisina uusina reitteinä/näkyminä saman `novi`-sovelluksen sisään, ei erilliseen kansioon.
- Viedään tuotannossa asiakkaan omalle domainille ja omalle hostaukselle, Laravelin oma sisäinen `novi/public`-kansio toimii tavalliseen tapaan verkkoon näkyvänä juurena (ei siis mitään ERILLISTÄ ylätason `public`-kansiota `novi`:n rinnalle).
- Perustelu: yksinkertaisin, luotettavin, ei Stripe-iframe-ongelmia, ei alidomainien/DNS:n säätämistä, nopein toteuttaa kuukauden aikataululla.

**TULEVAISUUDEN ASIAKKAAT, joilla on JO olemassa oleva nettisivu jota ei haluta rakentaa uusiksi (esim. valmis WordPress-sivu) → SILLOIN iframe-ratkaisu (myöhemmin, ei nyt):**
- `novi` pysyy omana keskitettynä sovelluksenaan (Irman oma palvelin, oma alidomain per asiakas, oma tietokanta per asiakas), ja asiakkaan olemassa olevalle sivulle lisätään pieni `<iframe src="https://asiakas.sinuntuotteesi.fi/varaa">`-koodinpätkä ilman että heidän sivuaan tarvitsee koskea.
- Tekninen muistilappu myöhempää varten: `novi`:n `/varaa`-reitille pitää tuolloin erikseen sallia upottaminen toisen sivun kehykseen (X-Frame-Options), ja Stripe-maksuvaihe pitää tehdä JS:llä "puhkaisemaan" kehys (`window.top.location`), koska Stripe Checkout ei suostu näkymään iframen sisällä.

Eli: pilotti rakennetaan yksinkertaisesti yhtenä kokonaisuutena nyt, iframe-ratkaisu säästetään myöhempää, valmiin-sivun-omaavaa asiakasta varten.

### Kokonaisjärjestys (16.8., viimeisin vahvistus) – tässä järjestyksessä edetään

1. Ensin ne kolme aiemmin sovittua korjausta (kohta 3 alussa: etusivun erottelu+rasti, kalenterin varauslomake, hoitojakson pidennys/lyhennys).
2. Sitten kaikki puuttuvat julkiset osat SUORAAN `novi`-kansion sisään, samaan sovellukseen/tietokantaan kuin hallintapaneeli: etusivu + 5 muuta sivua (`novi/resources/views/sivut/`-tyyliin, reitit `novi/routes/web.php`:hen), julkinen ajanvarauslomake, asiakkaan taikalinkki-kirjautuminen, Stripe-kytkentä, kalenterin pinkki→vihreä-merkintä. Kaikki tiedostot samaan `novi`-kansioon, EI erillistä pohjaa vielä tässä vaiheessa.
3. VASTA KUN pilottiasiakkaan koko `novi`-kokonaisuus on valmis, testattu ja käytössä: siitä tehdään erillinen, siivottu KOPIO/POHJA ("template") seuraavaa asiakasta (toista lemmikkihoitolaa) varten. Tämä pohja on lähtökohta jokaiselle uudelle asiakastyölle jatkossa — kloonataan siitä, ei aloiteta tyhjästä.
4. Ei siis tehdä pohjaa/templatea nyt etukäteen — se tehdään VASTA valmiin pilotin pohjalta, kun nähdään mikä oikeasti toimii käytännössä.

## 6. LOPULLINEN TYÖJÄRJESTYS (16.8. ilta) — JATKETAAN TÄSTÄ SEURAAVALLA KERRALLA

Pilottiasiakkaan nimi: **Missukan lemmikkihoitola**.

**Vaihe 1 – korjataan ensin hallintapaneelin kolme kesken olevaa kohtaa:**
- Etusivu: muistutukset ja saapumiset/lähdöt omiksi osioikseen, rasti poistaa rivin (koskee myös saapumiset/lähdöt-listaa, ei vain muistutuksia).
- Kalenterin varauslomake: puhelinnumeron muotoilu, paluu etusivulle (korjaa oikeaan kohteeseen – nyt menee virheellisesti etusivulle eikä kalenterisivulle), selattava kalenteri, uuden lemmikin lisäys.
- Hoitojakson pidennys/lyhennys kesken hoidon (hoidossa olevan eläimen hoitojakson pituuden muutos).

**Vaihe 2 – rakennetaan ajanvarausjärjestelmä (julkinen puoli) ja liitetään se asiakashallintajärjestelmään, KAIKKI suoraan `novi`-kansioon, sama sovellus/tietokanta kuin paneeli. EI VIELÄ mitään nettisivun markkinointisisältöä, vain itse järjestelmä:**
- Julkinen ajanvarauslomake (uusi reitti, käyttää olemassa olevaa `AvailabilityServiceä` + `BookingHold`-mekanismia → ei päällekkäisvarauksia).
- Stripe-maksu heti varauksen yhteydessä (`PaymentController` + `StripeWebhookController` ovat jo olemassa ja lähettävät vahvistussähköpostin automaattisesti onnistuneesta maksusta – näitä ei tarvitse rakentaa uudelleen, vain kytkeä uuteen lomakkeeseen).
- Asiakkaan taikalinkki-kirjautuminen (kertakäyttöinen linkki sähköpostiin, EI salasanaa – Irman valinta, koska helpoin asiakkaalle). Oma "customer"-guard, erillinen henkilökunnan kirjautumisesta.
- Kalenterin merkintä: uudet nettivaraukset vaaleanpunaisena kunnes yrittäjä avaa/klikkaa varauksen, sitten vihreäksi. Pieni "X uutta varausta" -laskuri kalenterin päälle. Vaatii uuden `acknowledged_at`-kentän `bookings`-tauluun (`confirmation_channel`-kenttä on jo valmiina, arvo esim. "online" vs "admin").

**Vaihe 3 – kun koko kokonaisuus (paneeli + ajanvarausjärjestelmä yhdistettynä) toimii ja on testattu:** tallennetaan se PUHTAANA POHJANA ("template") seuraavaa asiakasta varten – ENNEN kuin mitään nettisivun markkinointisisältöä lisätään mihinkään. Pohja = kaikki tähän mennessä rakennettu järjestelmä, ei minkään yksittäisen asiakkaan tekstejä/kuvia/brändisisältöä.

**Vaihe 4 – vasta pohjan tallennuksen jälkeen:** aloitetaan Missukan lemmikkihoitolan oman nettisivun koodaaminen (etusivu + muut sivut, tarkka sivumäärä/sisältö sovitaan silloin) suoraan `novi`:n (tämän asiakkaan työkopion) sisään, sekä hänen brändiasetuksensa (värit, nimi, yhteystiedot) hallintapaneelin Yritystiedot-sivulle.

## 8. Julkisen ajanvarausjärjestelmän tarkka virtaus (19.8., vahvistettu Irman kanssa)

Vaihe 1 (kolme paneelikorjausta) ja iso Varaukset/Palvelut-sivujen uudistus + peruutus/automatiikka on nyt valmis ja committoitu. Aloitettu Vaihe 2 (julkinen ajanvarausjärjestelmä).

**Tunnistautuminen: TAIKALINKKI, vahvistettu Irman toimesta (ei pelkkä puhelin/nimi-haku).**
- Syy: pelkkä puhelinnumero/nimi julkisella lomakkeella olisi tietoturvariski (kuka tahansa voisi arvata/tietää toisen numeron ja nähdä tämän lemmikkien terveystiedot).
- Uusi asiakas: ei vaadita sähköpostivarmistusta (ei ole vielä mitään suojattavaa dataa) – täyttää tiedot suoraan.
- Palaava asiakas (sähköposti löytyy jo `customers`-taulusta): järjestelmä lähettää kertakäyttöisen, ajastetun linkin (Laravelin `URL::temporarySignedRoute`, ei erillistä magic_links-taulua) sähköpostiin. Asiakas klikkaa linkkiä ja pääsee vasta silloin näkemään/muokkaamaan valmiiksi täytettyä lemmikkikorttiaan.

**Koko varausvirtaus (sama uusille ja palaaville asiakkaille, paitsi tunnistautumiskohta):**
1. Eläinten laji + lukumäärä, hoidon kesto/tyyppi (sama logiikka kuin paneelin sisäisessä velhossa).
2. Vapaan ajan haku (`AvailabilityService::findStartDates()`, sama moottori kuin paneelissa – ei voi tulla päällekkäisvarauksia).
3. Valitun ajan 10 min väliaikaisvaraus (`BookingHold`, jo olemassa, vapautuu itsestään jos ei viedä loppuun).
4. Sähköposti kysytään → tarkistetaan onko asiakas jo olemassa:
   - Ei löydy → täytetään asiakas- ja lemmikkikortti(t) tyhjästä (nimi, puhelin, lemmikkien perustiedot + terveys/hoitotiedot – samat kentät kuin paneelin eläinkortilla).
   - Löytyy → taikalinkki sähköpostiin → linkin klikkauksen jälkeen valmiiksi täytetty lemmikkikortti näytetään, asiakas voi muokata tietoja, lisätä lisäpalveluita tälle hoitojaksolle, kirjoittaa vapaata tekstiä.
5. "Varaa hoito" -painike → luodaan `Booking` (status=pending) + `BookingParticipant`-rivit, hold vapautetaan, ohjataan Stripe Checkoutiin ennakkomaksua varten (uudelleenkäyttää `PaymentController`/`payment.checkout`-reittiä, ei rakenneta uudelleen).
6. Stripe-maksu onnistuu → olemassa oleva `StripeWebhookController` hoitaa lopun automaattisesti: `status=confirmed`, `deposit_paid_at`, vahvistussähköposti asiakkaalle (`BookingConfirmed`-mail, jo valmis).
7. Jos maksu jää tekemättä määräaikaan mennessä → jo rakennettu `bookings:cancel-expired`-ajastus peruuttaa varauksen automaattisesti (ei uutta koodia tarvita).

**Tyylit, KORJATTU 19.8. illalla (tämä kumoaa yllä olevan alkuperäisen maininnan companies-taulusta):** julkinen ajanvarauslomake EI käytä `Company`-tietokantamallia (`companies.primary_color` ym.) eikä hallintapaneelin omaa kiinteää väriä. Se käyttää olemassa olevaa, koko sovelluksessa jo valmiiksi toimivaa bränditiedostojärjestelmää: `project/brand.php` (sijaitsee `novi`-kansion ULKOPUOLELLA, samassa kansiossa kuin tämä muistiinpanotiedosto), joka jaetaan kaikkiin näkymiin automaattisesti `ShareCompanyBranding`-middlewaren kautta muuttujina `$brand` (taulukko: primary_color, secondary_color, accent_color, background_color, text_color, font_heading, font_body) ja `$company` (taulukko: name, short_name, email, phone, business_id). `.env`:ssä `NOVI_BRAND_SOURCE=website` = "koodattu verkkosivu" -tila (asiakkaan oma koodattu sivu, ei WordPress) – tämä on jo oikea asetus, ei muuteta.

**TÄRKEÄ UUSI PÄÄTÖS 19.8. illalla: kaksi eri brändiä, eivät koskaan samat.**
- **Hallintapaneeli** (`layouts/app.blade.php`, `layouts/guest.blade.php`, kirjautumisen takana) = Novin OMAT, KIINTEÄT värit aina, eivät koskaan riipu asiakkaan verkkosivun brändistä. Ei enää lue `$brand`-muuttujaa ollenkaan – värit kirjoitetaan suoraan `:root`-CSS-blokkiin.
- **Julkinen ajanvarauslomake** (`/varaa`, `components/layouts/public.blade.php`) = lukee `$brand`/`$company`-muuttujat (jo automaattisesti saatavilla kaikissa näkymissä, EI tarvitse antaa erikseen controllerista `view()`-kutsussa).
- Nämä kaksi eivät ole koskaan samannäköisiä, eivätkä koskaan käytä samaa väriasetusta.

**UUSI PÄÄTÖS 19.8. illalla — verkkosivun oma "esikysely", periaatepäätös tehty, tarkennetaan kun oikea verkkosivu suunnitellaan:** Irma haluaa että tulevaisuudessa asiakkaan oma koodattu verkkosivu voi sisältää PIENEN oman kaavakkeen (eläinmäärä/laji + hoidon kesto), rakennettu suoraan verkkosivun omalla tyylillä osana verkkosivun koodia. Tämä pieni kaavake lähettää tiedot suoraan noviin, samaan osoitteeseen jota novin oma Vaihe 1 -lomake jo käyttää (`POST /varaa/vapaat-ajat`, kentät: `animals[][species]`, `care_type`, `duration_amount`, `duration_unit`). Novi ottaa siitä eteenpäin (vapaat päivät, hold, tunnistautuminen jne). Käyttäjä ei huomaa siirtymää kahden koodikannan välillä. Kenttänimien pitää täsmätä tarkalleen – jos jompikumpi puoli muuttuu, toisen pitää pysyä perässä. EI rakenneta vielä – vasta kun oikean verkkosivun ulkoasu suunnitellaan.

**Deployment-suunnitelma, vahvistettu 19.8.:** novi viedään Polar55-webhotellille (sama tili kuin asiakkaan koodattu verkkosivu). Polar55 tukee SSH:ta, useita PHP-versioita, cronia, Git-kloonausta, cPanelia – kaikki mitä novi tarvitsee. Novi ja verkkosivu pysyvät AINA kahtena täysin erillisenä kansiona samalla tilillä (esim. `/home/kayttaja/public_html/` = verkkosivu, `/home/kayttaja/novi/` = koko novi omana kansionaan), EIVÄT koskaan samassa kansiossa. Alidomaini (esim. `varaa.missukanlemmikkihoitola.fi`) luodaan ja sen "Document Root" osoitetaan NIMENOMAAN `novi/public`-alikansioon (Laravelin ainoa julkinen kansio – loput, mm. `.env`, eivät koskaan saa olla selaimesta saavutettavissa). Novi ja verkkosivu yhdistyvät käyttäjälle VAIN tavallisen linkin/napin kautta verkkosivulla ("Varaa hoitoaika" -nappi → vie alidomainiin) – ei mitään tiedostotason yhteyttä. Cron-rivi (`php artisan schedule:run` joka minuutti) pitää lisätä palvelimen cPaneliin käyttöönoton yhteydessä (paikallisesti Herd hoitaa tämän automaattisesti).

**Rakennusjärjestys jota seurataan (iso kokonaisuus pilkottu osiin, yksi osa kerrallaan "tehty"-vahvistuksella):**
1. Perusta: julkiset reitit (ei auth-middlewarea), kevyt julkinen layout (ei admin-sivuvalikkoa), `PublicBookingController`-runko.
2. Vaihe 1: eläinten laji/määrä + hoidon kesto -lomake.
3. Vaihe 2: vapaan ajan haku + 10 min hold.
4. Tunnistautuminen: sähköposti → uusi vs. taikalinkki.
5. Asiakas-/lemmikkikortti (uusi tai esitäytetty) + lisäpalvelut + vapaa teksti.
6. Varauksen tallennus + Stripe-uudelleenohjaus.
7. Kiitos-/vahvistussivu (webhook hoitaa lopun jo valmiiksi).

Ei vielä lisätä mitään Missukan lemmikkihoitolan omaa sisältöä (etusivun tekstit/kuvat) – vain puhdas järjestelmä, pohjan tallennus vasta tämän jälkeen (ks. kohta 6, Vaihe 3).

**Asiakasnäkyvyys, vahvistettu 19.8.:** eläinkortilla on jo valmiiksi kaksi erillistä kenttää: `general_notes` ("Asiakkaan tiedot", näkyy asiakkaalle) ja `internal_notes` ("Hoitolan muistiinpanot", vain yrittäjälle). Julkinen/palaavan asiakkaan lomake (kohta 5 yllä) saa näyttää ja muokata VAIN `general_notes`-kenttää – `internal_notes` ei koskaan tule julkiseen näkymään. Asiakaskortin `notes`-kenttä on toistaiseksi vain admin-puolella, ei näy julkisella lomakkeella.

**Arkkitehtuuri, vahvistettu 19.8.:** `/varaa`-reitit ovat samassa `novi`-Laravel-sovelluksessa kuin hallintapaneeli, sama tietokanta, ei erillinen projekti – ainoa ero on ettei niissä ole `auth`-middlewarea. Kun etusivu (kohta #27) rakennetaan myöhemmin, se on vain uusi `GET /`-reitti samaan sovellukseen, jossa nappi linkkaa `/varaa`-osoitteeseen.

**"Lisätietoa tälle hoitojaksolle" -kenttä, vahvistettu 19.8.:** ei saa koskaan esitäyttyä vanhasta varauksesta, vaikka palaava asiakas löytyisi haulla. Ratkaisu ilman tietokantamuutosta: tämä teksti tallennetaan `BookingParticipant.notes`-kenttään (per-varaus-rivi, luodaan aina uutena joka varaukselle) – EI `Pet.general_notes`-kenttään, joka on lemmikin pysyvä profiilitieto ja esitäytetään normaalisti. `Pet.internal_notes` ("Hoitolan muistiinpanot") pysyy erillään, pysyvänä, vain yrittäjän luettavissa, ei liity tähän kenttään mitenkään. Yhteenveto vaiheeseen 5: Pet-kentät (general_notes ym.) esitäytetään palaavalle asiakkaalle, BookingParticipant.notes-kenttä on AINA tyhjä lomakkeen avautuessa.

## 7. Sovittu toimintatapa koko tälle projektille (miten päätettiin edetä, 16.8. ilta keskustelu)

- `novi`-kansioon EI lisätä yhtään nettisivun/markkinoinnin sisältöä ennen kuin puhdas järjestelmä (paneeli + ajanvarausjärjestelmä) on valmis JA tallennettu pohjaksi. Tämä oli väärinymmärrys kesken keskustelun (Claude ehdotti aluksi sivun rakentamista samaan aikaan) – Irma korjasi: järjestelmä ensin, pohja talteen, VASTA SITTEN sivusisältö.
- Pohja = puhdas, uudelleenkäytettävä ydinjärjestelmä (ajanvaraus, asiakashallinta, laskutus, kalenteri, palvelut, raportit, asetukset, julkinen ajanvarauslomake, asiakaskirjautuminen). Ei kenenkään yksittäisen asiakkaan tekstejä/kuvia/brändisisältöä.
- Ei iframea, ei erillistä ylätason `public`-kansiota tässä pilotissa – kaikki yhtenä Laravel-sovelluksena `novi`-kansion sisällä (ks. kohta 5 "Kansiorakennepäätös"). Iframe säästetään myöhempää asiakasta varten, jolla on jo valmis nettisivu.
- Jatketaan aina yksi tehtävä kerrallaan, "tehty"-vahvistuksella ja tarkistuksella ennen seuraavaan siirtymistä.
- Claude ei koskaan muokkaa `novi`-kansion tiedostoja suoraan – ainoa poikkeus on tämä muistiinpanotiedosto.

## 9. Tilanne 19.8. illan lopussa – mitä on TEHTY ja mitä ON VIELÄ TEKEMÄTTÄ

**TEHTY ja testattu selaimessa toimivaksi (Irma vahvisti, koko ketju 1→2→3 toimii):**
- Reitit `novi/routes/web.php`: `/varaa` (GET), `/varaa/vapaat-ajat` (POST), `/varaa/hold` (POST), kaikki ilman auth-middlewarea.
- `novi/app/Http/Controllers/PublicBookingController.php`: `start()`, `availability()`, `hold()` -metodit.
- `novi/resources/views/components/layouts/public.blade.php` – HUOM: oikea polku on `components/layouts/`, EI `layouts/` (anonyymit Blade-komponentit haetaan aina `components`-kansiosta, tästä tuli aiemmin virhe "Unable to locate a class or view for component [layouts.public]").
- `novi/resources/views/public/booking/step1.blade.php`, `step2.blade.php`, `step3.blade.php`.
- Kaikki yllä olevat ovat kuitenkin vielä ALKUPERÄISESSÄ, yksinkertaisessa versiossaan (Company-mallia käyttäen, ei vielä tyylikkäämpää ulkoasua).

**EI VIELÄ TEHTY – Irma ei ole vienyt näitä VS Codeen (jatketaan näistä huomenna):**
1. `novi/resources/views/components/layouts/public.blade.php` – KOKO TIEDOSTON KORVAUS: tyylikkäämpi ulkoasu, "🐾 Lemmikkihoitolan ajanvaraus" -tunniste, vaihe-eteneminen (Vaihe X/5), oikea bränditiedosto (`$brand`/`$company`, ei `Company`-malli).
2. `novi/resources/views/public/booking/step1.blade.php` – KOKO TIEDOSTON KORVAUS: `:step="1"` lisätty, `:company`-proppi poistettu.
3. `novi/resources/views/public/booking/step2.blade.php` – KOKO TIEDOSTON KORVAUS: `:step="2"`, `<a href>` korvattu `<span onclick>`-linkillä.
4. `novi/resources/views/public/booking/step3.blade.php` – KOKO TIEDOSTON KORVAUS: `:step="3"`.
5. `novi/app/Http/Controllers/PublicBookingController.php` – poista `use App\Models\Company;` -importti ja kaikki kolme `'company' => Company::first(),` -riviä (start/availability/hold-metodeista), koska `$brand`/`$company` ovat jo automaattisesti saatavilla kaikissa näkymissä `ShareCompanyBranding`-middlewaren kautta.
6. `novi/resources/views/layouts/app.blade.php` – hallintapaneelin `:root`-CSS-lohko: poista `$brand[...] ??`-viittaukset, kirjoita kiinteät Novi-arvot suoraan (`--brand-primary: #4F46E5;` jne., samat arvot kuin nykyiset fallback-arvot).
7. `novi/resources/views/layouts/guest.blade.php` – sama kiinnitys kuin kohdassa 6.

Tarkka koodi kaikkiin seitsemään kohtaan on jo kirjoitettu tämän keskustelun aiemmissa Claude-vastauksissa (Poistettava alue / Mitä liitetään tilalle -muodossa) – Claude toistaa ne uudelleen huomenna kun jatketaan, ei tarvitse etsiä niitä erikseen.

**Tämän päivän commit (19.8. ilta):** committoitiin se mikä on tähän mennessä tehty ja testattu (kohdat "TEHTY"-listasta yllä) – ei vielä niitä seitsemää tekemätöntä kohtaa.

## 10. Novin liiketoimintamalli – "pohja + toimialamoduulit", vahvistettu 19.8. illalla

Irma on varmistanut tämän useaan kertaan aiemminkin – tämä EI ole uusi päätös, vaan vahvistus samasta, jo aiemmin sovitusta suunnitelmasta (ks. kohta 6, "Sovittu toimintatapa": pohja talteen ennen sisältöä).

**Lopullinen tavoite:** myydä Novi paitsi muille lemmikkihoitoloille, myös MUILLE TOIMIALOILLE (esim. parturi). Tutkittiin 19.8. koko koodikanta läpi tätä varten (Explore-agentti, kattava läpikäynti) – yhteenveto löydöksistä:
- Hyvä uutinen: "yksi asennus = yksi asiakas = oma tietokanta" -malli on JO käytännössä koodin oletusarvo (`Company::first()`, kiinteä `project/brand.php`-tiedosto per asennus) – tätä ei tarvitse muuttaa.
- `BelongsToCompany`-trait (tehty projektin alussa, task #1) on suunniteltu eri malliin (yksi jaettu tietokanta, monta yritystä `company_id`:llä) – tarpeeton mutta harmiton nykymallissa, voidaan siistiä joskus myöhemmin, ei kiireellinen.
- Eläin/laji (`Pet`, `species`) on TÄLLÄ HETKELLÄ syvältä kovakoodattu koko koodiin (mm. `AvailabilityService` laskee kapasiteetin lajin mukaan). Tämä on täysin OK niin kauan kuin myydään muille lemmikkihoitoloille (sama tarve). Jos/kun mennään muille toimialoille, "Eläin"-käsite pitää yleistää (esim. geneerinen "Varauskohde") ja rakentaa oikea asetuksilla-ohjattava ominaisuusjärjestelmä (feature flagit) – tätä EI ole vielä olemassa lainkaan.

**Etenemisjärjestys, vahvistettu 19.8.:**
1. Lemmikkihoitolan järjestelmä (nykyinen työ – paneli + julkinen ajanvaraus + kaikki muut kesken olevat tehtävät) tehdään ENSIN kokonaan valmiiksi ja testatuksi.
2. VASTA SEN JÄLKEEN koodi eritellään kahteen kerrokseen: "pohja" (kaikille toimialoille yhteinen: kirjautuminen, kalenteri, asiakkaat, laskutus, asetukset, varausmoottori) ja "lemmikkihoitolan moduuli" (eläin/laji-spesifit osat, pakataan uudelleenkäytettäväksi kokonaisuudeksi kaikille tuleville lemmikkihoitola-asiakkaille).
3. Muille toimialoille (esim. parturi) rakennetaan myöhemmin OMA moduulinsa saman pohjan päälle, vasta kun ensimmäinen toimiala on opittu kunnolla.
4. Git-versionumerot (esim. Novi 1.0.0) otetaan käyttöön VASTA kun pohja + lemmikkihoitolan moduuli on eroteltu – ei ennen.

**Rekisteröityminen (`/register`), vahvistettu 19.8.:** Tärkeä erottelu – asiakkaan (lemmikin omistajan) EI tarvitse koskaan rekisteröityä mihinkään, hän tunnistautuu taikalinkillä (ks. kohta 8). `/register`-sivu koskee vain HALLINTAPANEELIN käyttäjätiliä (yrityksen työntekijä). Koska yksi asennus = yksi asiakas, tätä sivua ei tarvita julkisena tuotannossa lainkaan – päätetty että se suljetaan/poistetaan käytöstä kokonaan (julkinen `/register` pois), ja Irma luo ensimmäisen käyttäjätilin itse komentoriviltä (`php artisan tinker`) osana jokaisen uuden asiakkaan käyttöönottoa, vasta kun asiakas on oikeasti ostanut palvelun. Tämän jälkeen yrityksen oma pääkäyttäjä voi ITSE lisätä lisää työntekijätilejä sisäänrakennetulla "Lisää työntekijä" -toiminnolla hallintapaneelin sisällä (esim. asetussivulle, `CompanySettingsController`in yhteyteen) – ei tarvitse enää Irmaa joka kerta kun uusi työntekijä palkataan. Sama malli pätee kaikkiin tuleviin toimialoihin, ei ole toimialariippuvainen. EI toteuteta vielä – tehdään myöhemmin, ei kiireellinen juuri nyt.

## 11b. Novin virallinen tassukuvake (SVG), vahvistettu 20.8., PÄIVITETTY 20.8. illalla

**PÄIVITYS 20.8. illalla: yksittäinen tassu on POISTETTU KÄYTÖSTÄ.** Irma vahvisti: "se on Novin ainoa tassukuva, muita ei käytetä" — eli kahden tassun "kävelyjälki"-versio on nyt Novin AINOA virallinen SVG-tassukuvake, käytetään KAIKKIALLA (myös kohdissa joissa aiemmin oli yksittäinen tassu tai vanha malli). Yksittäisen tassun koodia (3 ympyrää + 1 soikio ilman `<g>`-ryhmitystä) ei enää käytetä missään uudessa paikassa.

**Novin ainoa virallinen tassukuvake — kaksi tassua rinnakkain ("kävelyjälki"):**
```html
<svg width="30" height="26" viewBox="-4 0 30 28" fill="white">
    <g transform="translate(-5,2) rotate(-10) scale(0.62)">
        <circle cx="7.5" cy="9" r="2.1"/>
        <circle cx="12" cy="6.8" r="2.1"/>
        <circle cx="16.5" cy="9" r="2.1"/>
        <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
    </g>
    <g transform="translate(10,4) rotate(28) scale(0.62)">
        <circle cx="7.5" cy="9" r="2.1"/>
        <circle cx="12" cy="6.8" r="2.1"/>
        <circle cx="16.5" cy="9" r="2.1"/>
        <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
    </g>
</svg>
```
Väri asetetaan `fill`-määreellä (`currentColor` = periytyy isäntäelementin tekstiväristä, tai kiinteä väri kuten `white`/`var(--brand-primary)` kontekstin taustan mukaan). `width`/`height`-arvot ja `viewBox` pysyvät samoina — Tailwindin kokoluokat (esim. `h-4 w-4`) ohjaavat lopullisen näkyvän koon, `viewBox`-suhde vain varmistaa oikean muodon.

**KOOT PÄIVITETTY 20.8. illalla — nämä ovat NYT ne oikeat, lopulliset koot (alkuperäiset olivat liian pieniä, Irma korjasi):**

- `public/booking/step1.blade.php` — "Lemmikki 1" (ent. Eläin 1) -kentän label: `<svg width="30" height="26" viewBox="-4 0 30 28" fill="currentColor">`
- `public/booking/step1.blade.php` — "Näytä vapaat ajat" -napin ikoni: `<svg width="40" height="34" viewBox="-4 0 30 28" fill="white">`
- `dashboard.blade.php` — "Saapuvat ja lähtevät tänään" -osion ikoni: `<svg viewBox="-4 0 30 28" fill="var(--brand-primary)" class="h-7 w-7">`
- `dashboard.blade.php` — "Uusi varaus" -pikapainike: `<svg viewBox="-4 0 30 28" fill="currentColor" class="h-7 w-7">`
- `dashboard.blade.php` — "Uusi kuitti" -pikapainike: `<svg viewBox="-4 0 30 28" fill="currentColor" class="h-7 w-7">`
- `dashboard.blade.php` — "Uudet palvelut" -pikapainike: `<svg viewBox="-4 0 30 28" fill="currentColor" class="h-7 w-7">`
- `layouts/navigation.blade.php` — sivuvalikon käyttäjäavatar: `<svg viewBox="-4 0 30 28" fill="white" class="h-6 w-6">`

Sisäinen `<g>`-rakenne (kaksi tassua) on kaikissa sama, ks. koodiblokki yllä — vain ulkokuoren `<svg>`-rivi (koko + fill) vaihtelee paikan mukaan.

**Emojipohjaiset tassut jätetään edelleen koskematta**, vahvistettu 20.8.: yläpalkin "🐾 Lemmikkihoitolan ajanvaraus" (components/layouts/public.blade.php) ja hallintapaneelin sivuvalikon logo (layouts/navigation.blade.php, `🐾` sivuvalikon yläosassa) käyttävät edelleen tavallista 🐾-emojia, EI tätä SVG:tä — näitä ei muuteta.

**Eläin → Lemmikki -sananvaihto, tehty 20.8.:** kaikki käyttäjälle näkyvä "eläin"-sana (julkinen lomake + koko hallintapaneeli) vaihdettu muotoon "lemmikki" (esim. "Eläinten määrä" → "Lemmikkien määrä", "Eläinryhmät" → "Lemmikkiryhmät", flash-viestit ym.). TIETOISESTI JÄTETTY ENNALLEEN: "Eläinlääkäri" (virallinen ammattinimike, ei yleinen eläin-viittaus), yrityksen nimi "Eläinten hoitola Liperi" ja asetussivun esimerkkiplaceholder "Liperin Eläinhoitola Oy", sekä koodikommentit (eivät näy käyttäjälle).

## 11. KRIITTINEN, PITKÄN AIKAVÄLIN TAVOITE – Novin liiketoiminta- ja päivitysmalli (vahvistettu 19.8. illalla, Irman oma sanamuoto, EI SAA UNOHTUA)

Irma painotti erikseen että tämä on erittäin tärkeä asia joka pitää olla mielessä KAIKISSA tulevissa rakenneratkaisuissa, vaikka itse toteutusjärjestys on päätetty (ks. alla "Päätetty etenemisjärjestys"). Kirjataan siis koko malli tarkasti talteen.

**Irma oli aluksi eri mieltä ehdotuksestani** rakentaa ensin täysin lemmikkihoitola-spesifinen, kovakoodattu järjestelmä ja vasta sen jälkeen erottaa siitä geneerinen pohja. Irman kanta, hänen omin sanoin: *"minä en tee mitään lemmikkihoitolalle tehdyllä kovakoodatulla koodilla. haluan että myös ensimmäinen myytävä tuote on aivan samalla pohjalla ja lemmikkhoitola moduulilla kun sen jälkeen tulevat muut järjestelmät. en myy erilaista järjestelmää mihinkään en edes ensimmäistä!!!"* Tämä on siis lopullinen, ei-neuvoteltava vaatimus: EI KOSKAAN myydä yhdellekään asiakkaalle (ei edes ensimmäiselle, Missukalle) versiota joka poikkeaa rakenteellisesti muista – kaikki, myös ensimmäinen myytävä tuote, on rakennettava samalle pohjalle + moduulille jota kaikki myöhemmätkin asiakkaat käyttävät.

**Päätetty etenemisjärjestys (Irma hyväksyi tämän perustellun riskiarvion jälkeen):** koska geneerisen pohjan suunnitteleminen ARVAAMALLA, ilman yhtään valmista toimivaa toimialaesimerkkiä, on riskialttiimpaa kuin sen poimiminen yhdestä oikeasti valmiiksi rakennetusta ja testatusta esimerkistä (lemmikkihoitola), edetään näin: (1) lemmikkihoitolan järjestelmä viimeistellään ensin täysin valmiiksi ja testatuksi konkreettisena kokonaisuutena, (2) VASTA SITTEN erotetaan siitä pohja + lemmikkihoitolan moduuli. TÄRKEÄÄ: tämän välivaiheen (1) aikana ei saa lisätä UUTTA, tarpeetonta kovakoodausta yleisiin osiin (kirjautuminen, laskutus, asetukset, kalenterin runko) – lemmikki-spesifit asiat pidetään siististi omissa, jo nyt melko hyvin eristetyissä paikoissaan (`AvailabilityService`, `Pet`-malli, `species`-kentät), jotta myöhempi erottelu on turvallinen ja nopea, ei iso uudelleenkirjoitusprojekti.

**Lopullinen tavoitetila, Irman kuvaamana (tiivistettynä hänen viesteistään):**

*Yksi Novin pääkoodi, monta erillistä asennusta.* Irmalla on yksi Novin "pääversio" omassa kehitysympäristössä (versionumerolla, esim. Novi 1.0). Kun Novi myydään asiakkaalle (esim. Missukka), asiakkaan palvelimelle viedään TÄSMÄLLEEN tämä sama ohjelmakoodi, plus asiakkaan oma, täysin erillinen tietokanta. Kun Novi myydään toiselle asiakkaalle (esim. parturi), sinne viedään SAMA ohjelmakoodi, plus parturin oma erillinen tietokanta. Tietokannat eivät ole missään yhteydessä toisiinsa. Novi 1.0 → Missukan asennus → Missukan tietokanta. Novi 1.0 → Parturin asennus → Parturin tietokanta.

*Ei koskaan erillisiä koodihaaroja per asiakas.* EI koskaan saa syntyä tilannetta jossa "Novi-Missukka", "Novi-Parturi", "Novi-Hieroja" ovat käytännössä eri ohjelmia joissa esim. kalenterin koodi on kirjoitettu eri tavalla. Sen sijaan Novin pääkoodi osaa KAIKKI ominaisuudet (eläinpaikat, työntekijävalinta, verkkomaksu jne.), ja asiakkaan omissa ASETUKSISSA määrätään mitä ominaisuuksia kyseisellä asiakkaalla on käytössä. Esimerkki: Missukalla "lemmikkirekisteri: kyllä, työntekijävalinta: ei, verkkomaksu: kyllä"; Parturilla "lemmikkirekisteri: ei, työntekijävalinta: kyllä, verkkomaksu: kyllä" – mutta molemmilla on täsmälleen sama Novi 1.0 -ohjelmakoodi, ero on vain asetuksissa.

*Versionhallinta (Git) on pakollinen.* Novin lähdekoodi pidetään Gitissä (yksityinen repositorio, esim. GitHub – ei tarvitse olla julkinen). Git pitää kirjaa jokaisesta koodimuutoksesta (mitä tiedostoja muuttui, mitä rivejä lisättiin/poistettiin, milloin). Versiot nimetään semanttisesti: Novi 1.0.0 (ensimmäinen julkaisu) → 1.0.1 (pieni virhekorjaus) → 1.1.0 (pienempi uusi ominaisuus) → 2.0.0 (iso muutos). Irma pitää kirjaa mikä versio kullakin asiakkaalla on käytössä (esim. "Missukka – Novi 1.4.2, Parturi – Novi 1.4.2, Hieroja – Novi 1.3.8"), jotta näkee heti kuka tarvitsee päivityksen.

*Päivitysprosessi, kun virhe löytyy tai ominaisuus lisätään (kaikille asiakkaille sama tapa):* (1) muutos tehdään Novin pääkoodiin kerran, (2) testataan omassa testiympäristössä, (3) otetaan varmuuskopio asiakkaan tietokannasta, (4) päivitetään asiakkaan palvelimen Novi-koodi uusimpaan versioon (esim. Git pull), (5) ajetaan Laravelin migraatiot jotka päivittävät asiakkaan tietokannan rakenteen automaattisesti tarvittaessa (esim. uusi kenttä "markkinointilupa" lisätään Missukan JA parturin tietokantaan samalla migraatiolla, kumpaakaan ei tarvitse käsin muokata), (6) tarkistetaan että kirjautuminen, varaus ja tärkeimmät toiminnot toimivat. Hyvin rakennettuna tämä on 10–30 minuutin huoltotoimenpide per asiakas, ei tuntikausien projekti – EI RIIPU siitä onko asiakkaita 10, 100 vai 1000, koska itse koodimuutos tehdään vain kerran.

*Asiakaskohtainen ulkoasu/tiedot pysyvät koskemattomina päivityksissä.* Asiakkaan logo, värit, hinnat, varausmaksuprosentti yms. eivät ole koskaan Novin ydinkoodissa, vaan asiakaskohtaisissa asetuksissa/tiedostoissa (ks. kohta 8 "Tyylit" ja `project/brand.php`-mekanismi) – niinpä koko Novin ohjelmakoodin voi päivittää esim. versiosta 1.4 → 1.5 ilman että minkään asiakkaan brändi tai asetukset katoavat tai sekoittuvat.

## 12. KESKEN, EI VIELÄ TEHTY (23.8. ilta): kaksi tarkastajalle tarkoitettua selontekoa

Irma pyysi kaksi pitkää, kopioitavaa, ei-koodi-selontekoa ulkopuoliselle tarkastajalle. Aloitin taustatutkimuksen (luin koodia läpi), mutta Irma keskeytti ennen kuin kirjoitin itse tekstit — **jatka tästä seuraavan kerran, älä aloita tutkimusta alusta, käytä alla olevia löydöksiä.**

**Selonteko 1 pyydetty sisältö (tekninen, kattava, ulkopuoliselle ymmärrettävä):**
1. Julkinen ajanvaraus alusta loppuun: mitä tapahtuu kun asiakas alkaa täyttää kaavaketta verkkosivulla, tekniikka tarkasti.
2. Miten kaavake on tarkoitus viedä asiakkaan verkkosivun alaosaan (upotus), mitä se vaatii selaimelta.
3. Mitä varmistuksia on tehty, että koko systeemi (julkinen lomake + hallintapaneelin kalenteri/varaukset) toimii täydellisesti yhteen (esim. ei päällekkäisvarauksia).
4. Yrittäjän varausjärjestelmä taustatoimintoineen, pienintä yksityiskohtaa myöten (esim. miten uusi asiakasmerkintä ilmestyy kalenteriin).
5. Hallintapaneelin toiminnot asiakkaan (yrittäjän) avuksi, ja miten sieltä muutetaan brändiasetukset.
6. Miten ajanvarauslomake hakee tyylinsä `project`-kansiosta — koko tekniikka.

**Selonteko 2 pyydetty sisältö:** miten koko myytävä järjestelmä (pohja + lemmikkihoitola-moduuli + asiakaskerros) on tarkoitus rakentaa, miten ne toimivat yhteen, ja miten niitä päivitetään jatkossa. (Tämä on jo pitkälti kirjattu yllä kohtiin 10 ja 11 — selonteko 2 on käytännössä näiden kahden kohdan puhtaaksikirjoitus tarkastajalle sopivaan, juoksevaan muotoon, EI uutta sisältöä, vain jäsennelty selitys.)

**Tutkimuslöydökset 23.8. (käytä näitä suoraan, ei tarvitse tutkia uudelleen):**

*Brändi/väri-lähteitä on koodissa itse asiassa KOLME erillistä, ei kaksi — tämä on tärkeä yksityiskohta tarkastajalle:*
1. **Hallintapaneeli** (`layouts/app.blade.php`, `layouts/guest.blade.php`) — kiinteät, koodiin kirjoitetut Novi-värit `:root`-CSS:ssä, eivät lue mitään tiedostoa tai tietokantaa. Kommentti koodissa: "Novi-hallintapaneelin OMAT, kiinteät värit — eivät koskaan riipu asiakkaan verkkosivun brändistä."
2. **Julkinen ajanvarauslomake** (`components/booking-widget.blade.php`) — lukee `$brand`-muuttujan, joka tulee `App\Core\Branding\BrandManager`-luokan kautta tiedostosta `project/brand.php` (polku ratkaistaan `config/branding.php`:n kautta, `base_path('../project/brand.php')`, eli `novi`-kansion ULKOPUOLELTA). Jaetaan kaikkiin näkymiin automaattisesti `ShareCompanyBranding`-middlewarella (`bootstrap/app.php`:ssä globaalisti kaikille web-reiteille). `$company`-muuttuja tulee samalla tavalla tiedostosta `project/company.php` (nimi, sähköposti, puhelin, y-tunnus — pelkkää yritystunnistetietoa, ei värejä).
3. **Kuitit/laskut (PDF + `invoices/show.blade.php`)** — käyttävät KOLMATTA lähdettä: `App\Models\Company`-tietokantamallin omia sarakkeita `primary_color`/`secondary_color` (esim. `$invoice->company->primary_color`). Näitä muokataan hallintapaneelin Asetukset → Yritystiedot-välilehdeltä (`CompanySettingsController::updateCompanyInfo()`). Nämä EIVÄT ole samat arvot kuin `project/brand.php`:ssä — ne on tarkoituksella pidetty erillään (kuitin väri voi teoriassa poiketa verkkosivun väristä, esim. jos yritys haluaa mustavalkoisen kuitin mutta värikkään nettisivun).

Eli: hallintapaneeli = aina kiinteä Novi-brändi; julkinen lomake = `project/brand.php`-tiedosto; kuitit/laskut = `Company`-tietokantarivin omat värikentät. Kolme eri mekanismia, kolme eri tarkoitusta — tämä pitää selittää tarkastajalle selkeästi ettei sekoitu.

*Muut jo luetut/varmistetut tekniset yksityiskohdat joita selonteko 1 tarvitsee (koodi käyty läpi 23.8., ei tarvitse lukea uudelleen):*
- `AvailabilityService`: koko kapasiteettilaskennan logiikka (`fits()`, `findStartDates()`, `isAvailable()`, `usageForDate()`) — laskee per-laji, per-päivä, ottaa huomioon sekä vahvistetut/pending-varaukset (`BookingParticipant`, pl. peruutetut) että aktiiviset 10 min hold-varaukset (`BookingHold`, ei-vanhentuneet). Tämä on SE YKSI paikka joka takaa ettei julkinen lomake ja hallintapaneelin oma varausvelho voi koskaan aiheuttaa päällekkäisvarausta — molemmat käyttävät samaa palvelua.
- `DashboardController` ja `CalendarController`: molemmat laskevat `has_new`/`new_booking_id` per kalenteripäivä samalla logiikalla (booking jonka `confirmation_channel === 'online'` ja `acknowledged_at` on tyhjä) — tästä syntyy "uusi varaus" -merkintä kalenteriin. `CalendarController::day()` merkitsee päivän varaukset "nähdyiksi" (`acknowledged_at = now()`) automaattisesti kun yrittäjä avaa päivänäkymän.
- `CompanySettingsController`: koko asetussivun taustalogiikka (varausmaksu-%, perushinta, yritystiedot+värit, lemmikkiryhmät/kapasiteetti, palvelut, muistutustyypit, hoitomuodot, ajanvarauslomakkeen kenttien päällä/pois-kytkimet) — kaikki tallentuu joko suoraan `Company`-riville tai sen `settings`-JSON-kenttään.
- `booking-widget.blade.php`: koko CSS on skoopattu `.novi-booking-widget`-luokan alle (ei bare `body`/`:root`-valitsimia), jotta komponentti on turvallinen upottaa isäntäsivun sisään ilman että se sotkee isäntäsivun tyylejä. TÄRKEÄ: itse upotusmekanismia (iframe isäntäsivulle) EI ole vielä rakennettu — se on suunniteltu (ks. kohta 5, "TULEVAISUUDEN ASIAKKAAT" -kappale) mutta ei toteutettu. Selonteko 1 pitää kirjoittaa niin että tämä ero (suunniteltu vs. jo toteutettu) on selvä.
- Stripe: `PaymentController::checkout()` (juuri 23.8. päivitetty: `expires_at` synkassa varauksen `payment_deadline`-kentän kanssa, min 30min/max 24h Stripen omien rajojen sisällä), `StripeWebhookController` (juuri korjattu: vahvistaa vain `status === 'pending'`-varauksen, ei voi enää herättää peruutettua varausta henkiin).
- Automaattinen peruutus: `bookings:cancel-expired`-komento, ajastettu `routes/console.php`:ssä 5 min välein, lähettää nyt (23.8. lisätty) `BookingPaymentExpired`-sähköpostin asiakkaalle kun varaus peruuntuu maksamattomana.

**Kun jatketaan:** kirjoita molemmat selonteot suoraan chattiin (ei tiedostoina present_files:lla, Irman aiemman ohjeen mukaisesti "kopioitava" = tekstiä chatissa). Käy tarvittaessa vielä läpi (ei vielä luettu tähän mennessä): `resources/views/layouts/navigation.blade.php` (sivuvalikko, mitä toimintoja siellä on), `settings/index.blade.php` koko sisältö, ja `public/booking/step1–step6.blade.php` -tiedostot yksityiskohtaisesti selonteko 1:n kohtaa 1 varten.

*Liiketoimintahyöty Irmalle:* koska päivitys on nopea ja sama joka asiakkaalle, Irma voi myydä sovittuja huoltokäyntejä (esim. 3–4 kertaa vuodessa per asiakas), joissa tarkistetaan palvelin/PHP/Laravel-versiot, riippuvuudet, varmuuskopiot, lokit, asennetaan uusin Novi-versio, ajetaan migraatiot, ja testataan kirjautuminen/varaus/sähköpostit/varmuuskopiointi. Asiakas maksaa vastuullisesta ylläpitotyöstä, vaikka itse tekninen päivitysaskel veisi vain 15–30 minuuttia – ja tämä malli skaalautuu 10:stä 1000:een asiakkaaseen ilman että ylläpitotyö kasvaa suhteettomasti, koska pohja+moduuli pysyy aina samana kaikilla.

**Mitä tämä vaatii koodilta, jota EI vielä ole (rakennetaan pohja+moduuli-erottelun yhteydessä, ks. yllä "Päätetty etenemisjärjestys"):** oikea asetuksilla/ominaisuuslipuilla ohjattava järjestelmä (`Company.settings`-kenttää pitää laajentaa pelkistä hinnoittelutiedoista myös ominaisuuskytkimiin, esim. `lemmikkirekisteri_kaytossa`, `tyontekija_valinta_kaytossa`), ja eläin/laji-käsitteen (`Pet`, `species`) yleistäminen geneerisemmäksi käsitteeksi jota eri toimialat voivat käyttää eri tavoin. Ei toteuteta nyt – vasta erotteluvaiheessa.

## 12. PAKOLLINEN käyttöönottolista ennen kuin novi viedään oikealle asiakkaalle tuotantoon (kirjattu 20.8.)

Nämä EIVÄT saa jäädä paikalliseen kehitysasetukseen kun sivu viedään asiakkaan omalle palvelimelle (esim. Polar55). Paikallinen `novi.test`/Herd-`.env` pysyy koskemattomana – näitä muutetaan VAIN tuotantopalvelimen omassa, erillisessä `.env`-tiedostossa käyttöönoton yhteydessä:

- `APP_DEBUG=false` (paikallisesti pidetään `true`, koska sen avulla on koko projektin ajan debugattu virheitä kuvakaappauksista – tuotannossa `true` näyttäisi asiakkaille/vierailijoille raakoja virhesivuja mm. tietokantatiedoilla, vakava tietoturvariski).
- `APP_ENV=production`.
- `MAIL_MAILER` vaihdettava oikeaan SMTP-palveluun (paikallisesti `log`, koska ei ole omaa SMTP-tiliä testausta varten – tuotannossa sähköpostit eivät lähde oikeasti mihinkään ennen tätä muutosta, mm. taikalinkki ja varausvahvistus).
- `STRIPE_KEY`/`STRIPE_SECRET`/`STRIPE_WEBHOOK_SECRET` vaihdettava Stripen oikeisiin live-avaimiin (nyt `pk_test_`/`sk_test_`-testiavaimet).
- Varmista Stripen webhook on rekisteröity oikeaan tuotanto-osoitteeseen (`https://asiakkaan-domain/stripe/webhook`) Stripen dashboardissa, ei enää paikallista `stripe listen`-komentoa.
- Cron-rivi (`php artisan schedule:run` joka minuutti) lisättävä palvelimen cPaneliin (paikallisesti Herd hoitaa tämän automaattisesti).

Tarkistettu 20.8. muuten kunnossa olevaksi: `.env` on gitignoressa, Stripe-webhook on CSRF-vapautettu ja allekirjoitus tarkistetaan, julkisilla `/varaa`-reiteillä on throttle-rajoitus (60/min, taikalinkin lähetys 5/min), kaikissa julkisen lomakkeen vaiheissa on CSRF-token, kaksoisvarausta ei voi enää syntyä mistään kolmesta varauksentekopaikasta (julkinen lomake, kalenterin "Uusi varaus", entinen varausvelho on poistettu kokonaan käytöstä).

## 13. JATKETAAN HUOMENNA TÄSTÄ (kirjattu 20.8. illalla) – viimeinen tehtävä ennen pohja+moduuli-erottelua

**Viimeinen jäljellä oleva tehtävä ennen kuin lemmikkihoitolan järjestelmä on Irman omien kriteerien mukaan täysin valmis:**

1. **Tehtävä #31 – Hoitojakson pidennys/lyhennys kesken hoidon.** Hoidossa jo olevan lemmikin hoitojakson pituutta (lähtöpäivää) pitää voida muuttaa kesken hoidon, esim. asiakas soittaa ja haluaa jättää lemmikin pariksi päiväksi lisää tai hakea aiemmin. Ei aloitettu ollenkaan – suunnitellaan ja toteutetaan huomenna ensimmäisenä. Muista kapasiteettitarkistus (`AvailabilityService::isAvailable()`) myös tälle: pidennys ei saa ylittää kapasiteettia niiden lisäpäivien osalta.

**Sen jälkeen, kun #31 on tehty ja testattu:**

2. **Koko järjestelmän perusteellinen lopputarkastus.** Käydään vielä kerran läpi koko kokonaisuus (julkinen ajanvaraus + hallintapaneeli) kunnolla: kaikki toiminnot testataan oikeasti selaimessa päästä päähän (ei vain koodista lukien), erityisesti nyt tehdyt viimeisimmät muutokset (kalenterin #30-ominaisuus, stepperin tassu-ulkoasu, Stripe-integraatio, kaksoisvarauksen esto kaikissa kolmessa varauksentekopaikassa). Tarkistetaan myös vielä kertaalleen kohdan 12 "Pakollinen käyttöönottolista" asiat ovat ajan tasalla.

3. **Vasta tämän jälkeen: pohja + lemmikkihoitolan moduuli erotellaan omaksi kokonaisuudekseen**, ks. kohta 10 ("Novin liiketoimintamalli – pohja + toimialamoduulit") ja kohta 11 (Irman ei-neuvoteltava vaatimus: ei koskaan myydä poikkeavaa versiota, edes ensimmäiselle asiakkaalle). Tässä vaiheessa tehdään myös ensimmäinen virallinen versionumero: **Novi 1.0.0** (ks. kohta 10, kohta 4: "Git-versionumerot otetaan käyttöön VASTA kun pohja + lemmikkihoitolan moduuli on eroteltu – ei ennen").

Järjestys on siis: #31 valmiiksi → koko systeemi tarkistetaan huolella → pohja/moduuli-erottelu → Novi 1.0.0.

## 14. Muistiinpanot 21.8.2026 – täydellinen bugikierros TEHTY, varauslaatikko siistitty upotusta varten

### Kohdan 13 kohta 2 ("koko järjestelmän perusteellinen lopputarkastus") NYT TEHTY

Käytiin läpi koko koodikanta rivi riviltä: kaikki kontrollerit, kaikki mallit, kaikki reitit ja niiden suojaukset, config-tiedostot. Löytyi ja korjattiin viisi asiaa:

1. **Etusivun minikalenterista puuttui "Uusi"-merkinnän laskenta** – `DashboardController::buildCalendarDays()` ei laskenut `has_new`/`new_booking_id`-tietoja, joten uudet nettivaraukset eivät näkyneet pinkkinä etusivulla, vain Kalenteri-sivulla. Korjattu, molemmat käyttävät nyt samaa logiikkaa.
2. **Avoin `/register`-sivu poistettu kokonaan.** Kuka tahansa netissä olisi voinut luoda itselleen tunnukset hallintapaneeliin, koska sivu ei ollut rajoitettu eikä uusi käyttäjä saanut `company_id`:tä. `routes/auth.php`:sta poistettu rekisteröitymisreitit, `RegisteredUserController.php` ja `auth/register.blade.php` poistettu tiedostoina.
3. **Vakava: IDOR-aukko julkisessa ajanvarauslomakkeessa.** `PublicBookingController::store()` haki lemmikkiä `pet_id`:n perusteella tarkistamatta kuuluuko se varauksen tekevälle asiakkaalle – piilokenttää muokkaamalla olisi voinut ylikirjoittaa KENEN TAHANSA toisen asiakkaan lemmikin tiedot. Korjattu: `Pet::where('customer_id', $customer->id)->find(...)`, ei löydy → luodaan uusi lemmikki sen sijaan.
4. **Vakava: kuitit/laskut olivat julkisesti selattavissa ilman kirjautumista.** `/kuitti/{invoice}`, `/kuitti/{invoice}/pdf`, `/kuitti/{invoice}/tulosta` eivät olleet `auth`-suojattuja, ja koska ID:t ovat juoksevia numeroita, kuka tahansa olisi voinut selata läpi kaikkien asiakkaiden kuitit (nimi, hinta, ALV). Varmistettu ettei mikään sähköposti linkitä näihin (turvallista lukita). Nyt `auth`+`verified`-suojattu.
5. **Maksun onnistumissivu paljasti asiakkaan sähköpostin.** `/maksu/onnistui?booking=X` näytti `$booking->customer->email`-arvon kenelle tahansa joka arvasi varauksen ID:n. Sähköpostin näyttäminen poistettu sivulta (pysyy julkisena reittinä, koska asiakas ohjautuu sinne Stripe-maksun jälkeen kirjautumatta, mutta ei enää näytä henkilötietoa).

Kaikki committoitu ja pushattu GitHubiin (commit "Tietoturvakorjaukset: pet_id-omistajuustarkistus, kuittien/laskujen auth-suojaus, rekisteröitymisen poisto, etusivun uudet-varaukset-korjaus").

**Kohta 13 kokonaisuudessaan päivitetty tilanne: jäljellä on enää kohta 1 (tehtävä #31) ennen pohja/moduuli-erottelua ja Novi 1.0.0:aa.**

### Varauslaatikon tekninen siivous upotusta varten – TEHTY (kolme osaa)

Ennen tehtävää #31 Irma halusi vielä viimeistellä sen mitä kohdassa "vaihe 2: korjataan varauslaatikko upotettavaksi siistiksi" oli sovittu. Kolme osaa tehtiin ja testattiin (Irma vahvisti: "kaikki toimii varaus vahvistettu"):

**1. Ajanvarauslomakkeen kentät muokattaviksi per ostava lemmikkihoitola.** Uusi `novi/config/public_booking_fields.php` listaa 14 valinnaista lemmikkikenttää (rotu, syntymäaika, sukupuoli, paino, mikrosiru, allergiat, lääkitys, ruokintaohjeet, käytöstiedot, eläinlääkärin nimi/puhelin, hätätilanneohjeet, muuta huomioitavaa, lisätietoa hoitojaksolle). Nimi ja laji ovat aina pakollisia, ei muokattavissa. Yritysasetuksiin uusi "Ajanvarauslomake"-välilehti (`CompanySettingsController::updatePublicBookingFields()`), tallentaa valinnat `company.settings['public_booking_fields']`-kenttään (sama JSON-asetuskenttä jota `deposit_percentage`/`base_daily_rate` jo käyttävät – ei uutta migraatiota tarvittu). Oletuksena kaikki 14 kenttää päällä, joten olemassa oleva lomake ei muuttunut miltään osin. `public/booking/step4.blade.php` lukee nyt nämä asetukset ja näyttää/piilottaa kentät sen mukaisesti.

**2. Varausmaksun päälle/pois-kytkin oli jo valmiiksi olemassa** – jos `deposit_percentage` asetetaan 0 %:iin, koko Stripe-maksuvaihe ohitetaan automaattisesti (`PublicBookingController::store()`). Ei vaatinut lisätyötä, todettiin vain toimivaksi.

**3. Varauslaatikko eriytetty omaksi `<x-booking-widget>`-komponentiksi** (`novi/resources/views/components/booking-widget.blade.php`, uusi tiedosto). `components/layouts/public.blade.php` on nyt pelkkä ohut sivupohja (`<!DOCTYPE html>`/`<head>`/`<body>`), joka kutsuu `<x-booking-widget>`:a sisällään. Kaikki CSS-valitsimet skoopattu `.novi-booking-widget`-luokan alle (myös vaarallinen bare `body { ... }` -valitsin, joka olisi upotettaessa voinut rikkoa isäntäsivun tyylit). Värit/fontit tulevat edelleen `project/brand.php`:sta `$brand`-muuttujan kautta, tätä ei muutettu. Kun Missukan (tai minkä tahansa asiakkaan) verkkosivu joskus rakennetaan, sinne pudotetaan tästä eteenpäin vain `<x-booking-widget>`, ei koko sivupohjaa.

### Kolmikerroksisen mallin tarkennus (21.8., Irman oma sanamuoto) – täydentää kohtaa 10 ja 11

Irma tarkensi tänään kolmikerroksisen mallin (pohja / lemmikkihoitola-moduuli / ostavan lemmikkihoitolan pohja) yksityiskohtaisemmin kuin aiemmin oli kirjattu. Tämä EI muuta aiempaa päätöstä, vaan täsmentää mitä kukin kerros konkreettisesti pitää sisällään:

- **Pohja** – puhtaasti tekninen perusta, huolehtii että kaikki moduulit toimivat samalla tavalla (kirjautuminen, reititys, perusrakenteet). Rakennettu niin että sitä on helppo päivittää jatkossa kaikille asiakkaille kerralla. Ei sisällä mitään lemmikkihoitola-spesifistä.
- **Lemmikkihoitola-moduuli** – sisältää itse toiminnallisuuden (ajanvaraus, kalenteri, asiakas-/eläinkortit, laskutus), rakennettu niin että seuraava kerros (ostavan lemmikkihoitolan säädöt) voi sitä helposti mukauttaa:
  - Kuvakkeet (esim. tassu) = Novin päättämiä ja kiinteitä, EI muokattavissa per asiakas.
  - Rakenne = Irman/Novin luoma, enimmäkseen valmis paketti.
  - Poikkeus: itse verkkosivulle upotettava laatikko (`<x-booking-widget>`) täytyy olla rakenteeltaan joustavampi kuin muu paneeli, koska sen pitää sopia visuaalisesti juuri sen asiakkaan verkkosivun tyyliin.
  - Mitä lomakkeella kysytään asiakkaalta = muokattavissa per ostava lemmikkihoitola (TEHTY TÄNÄÄN, ks. yllä "Ajanvarauslomakkeen kentät").
  - Toiminnot kuten varausmaksu = päälle/pois-kytkettävissä per ostava lemmikkihoitola (oli jo valmiina).
- **Ostavan lemmikkihoitolan pohja** – tuo väri/fontit heidän omalta verkkosivultaan (`project/brand.php`, ei muutu), ja säätää lemmikkihoitola-moduulin joustavat kohdat: lomakekentät (nyt konfiguroitavissa asetussivulta), toimintojen päälle/pois-kytkennät, ja upotettavan laatikon rakenteen sovituksen heidän sivunsa tyyliin.

**Tärkeä periaate joka nousi tästä keskustelusta:** moduulin koodiin pitää rakentaa "koukut" (asetuksia lukevat kohdat) jo ETUKÄTEEN, ei vasta pohja/moduuli-erottelun yhteydessä – muuten myöhempi asiakaskohtainen säätäminen vaatisi moduulin koodin muokkaamista uudestaan jokaiselle asiakkaalle, mikä rikkoisi periaatteen "ei koskaan rakenteellisesti eri versiota kenellekään" (ks. kohta 11). Tästä syystä lomakekenttien muokattavuus rakennettiin nyt asetuspohjaiseksi (`config/public_booking_fields.php` + `company.settings`), ei vasta myöhemmässä vaiheessa.

### Vielä yksi pieni siivouskohta varauslaatikkoon, EI VIELÄ TEHTY (kirjattu 21.8., muistiin myöhempää varten)

Julkisen lomakkeen alaosassa on vielä 4 kohdan "hyötynostot"-rivi (Rakkaudella hoidettu / Turvallinen ympäristö / Päivityksiä / Ammattitaidolla, ikoneina sydän/kilpi/kamera/mitali). Irma vahvisti 21.8.: nämä ovat upotuksessa tarpeettomia ja otetaan pois siistitystä kaavakkeesta. Ei poistettu vielä koodista – tehdään kun varauslaatikkoon palataan seuraavan kerran (todennäköisesti pohja/moduuli-erottelun yhteydessä tai juuri ennen sitä).

### Vielä yksi löytynyt bugi, EI VIELÄ TEHTY (kirjattu 21.8., valmis diffi odottaa)

Kalenterisivun "X uutta varausta" -pilli (otsikon alla, `calendar/index.blade.php`) ei tee mitään klikattaessa – pelkkä teksti, ei linkkiä. Pitäisi viedä kalenteri siihen kuukauteen/päivään jossa uusin käsittelemätön (online, ei kuitattu) varaus on, jonka jälkeen olemassa oleva "Uusi"-badge sillä päivällä (toimii jo) avaa asiakaskortin. Korjaus on jo suunniteltu ja valmiina liitettäväksi seuraavalla kerralla:

- `app/Http/Controllers/CalendarController.php` `index()`-metodiin: uusi `$firstNewBookingDate`-muuttuja, haetaan varhaisin `start_date` niistä varauksista joissa `confirmation_channel = 'online'`, `acknowledged_at` on tyhjä, `status != 'cancelled'`, muotoillaan `Y-m-d`-merkkijonoksi ja välitetään näkymään.
- `resources/views/calendar/index.blade.php`: pilli-`<span>` saa `onclick`-siirron `route('calendar.index', ['view' => 'month', 'date' => $firstNewBookingDate])`-osoitteeseen, jos `$firstNewBookingDate` on olemassa.

Tarkka koodi on jo kirjoitettu edellisessä Claude-vastauksessa (Poistettava alue / Mitä liitetään tilalle -muodossa) – toistetaan se kun tähän palataan, ei tarvitse suunnitella uudelleen.

### Seuraavaksi

Kohdan 13 mukaisesti: **tehtävä #31 (Hoitojakson pidennys/lyhennys kesken hoidon)** on nyt ainoa jäljellä oleva kohta ennen pohja + lemmikkihoitolan moduuli -erottelua ja Novi 1.0.0:aa. Ei aloitettu vielä. Lisäksi kaksi pientä kirjattua siivouskohtaa odottaa (hyötynostot-rivin poisto, kalenterin "X uutta varausta" -pillin korjaus yllä) – tehdään kun varauslaatikkoon/kalenteriin seuraavan kerran palataan.
