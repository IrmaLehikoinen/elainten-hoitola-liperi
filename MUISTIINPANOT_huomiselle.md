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
