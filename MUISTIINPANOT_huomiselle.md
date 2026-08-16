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

## 7. Sovittu toimintatapa koko tälle projektille (miten päätettiin edetä, 16.8. ilta keskustelu)

- `novi`-kansioon EI lisätä yhtään nettisivun/markkinoinnin sisältöä ennen kuin puhdas järjestelmä (paneeli + ajanvarausjärjestelmä) on valmis JA tallennettu pohjaksi. Tämä oli väärinymmärrys kesken keskustelun (Claude ehdotti aluksi sivun rakentamista samaan aikaan) – Irma korjasi: järjestelmä ensin, pohja talteen, VASTA SITTEN sivusisältö.
- Pohja = puhdas, uudelleenkäytettävä ydinjärjestelmä (ajanvaraus, asiakashallinta, laskutus, kalenteri, palvelut, raportit, asetukset, julkinen ajanvarauslomake, asiakaskirjautuminen). Ei kenenkään yksittäisen asiakkaan tekstejä/kuvia/brändisisältöä.
- Ei iframea, ei erillistä ylätason `public`-kansiota tässä pilotissa – kaikki yhtenä Laravel-sovelluksena `novi`-kansion sisällä (ks. kohta 5 "Kansiorakennepäätös"). Iframe säästetään myöhempää asiakasta varten, jolla on jo valmis nettisivu.
- Jatketaan aina yksi tehtävä kerrallaan, "tehty"-vahvistuksella ja tarkistuksella ennen seuraavaan siirtymistä.
- Claude ei koskaan muokkaa `novi`-kansion tiedostoja suoraan – ainoa poikkeus on tämä muistiinpanotiedosto.
