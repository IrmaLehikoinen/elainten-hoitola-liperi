# Uuden asiakkaan käyttöönotto — tarkistuslista

Tätä listaa seurataan aina kun "novi"-järjestelmä (pohja + jokin toimialamoduuli, esim. Kurssit tai Lemmikkihoitola) myydään ja viedään uudelle asiakkaalle omaan erilliseen asennukseen.

**Tärkeä huomio testidatasta:** `elainten-hoitola-liperi`-kansio on Irman OMA tuotantoympäristö (Testihoitola = oma lemmikkihoitola, Sydänpolku = oma kurssiliiketoiminta). Näiden yritysten tiedot (mukaan lukien testikäyttäjä `testihenkilokunta@example.com`) ovat pelkkiä tietokantarivejä — ne EIVÄT ole missään git-commitissa tai -tagissa, koska git seuraa vain koodia, ei tietokannan sisältöä. Uudelle asiakkaalle tehdään aina täysin tyhjä, uusi tietokanta (kohta 1 alla), joten mikään Testihoitolan/Sydänpolun testidatasta ei koskaan päädy uuden asiakkaan asennukseen riippumatta siitä mitä git-versiota käytetään.

## 0. Uuden asiakkaan oma kansio ja git-haara (tee tämä ENSIN, ennen kohtaa 1)

Älä koskaan käytä `elainten-hoitola-liperi`-kansiota (se on oma tuotantosi). Jokainen uusi asiakas saa oman kokonaan erillisen kansion koneellasi ja oman git-haaran:

1. Luo uudelle asiakkaalle haara `main`-haarasta (ks. tarkemmin `PAIVITYSTEN_HALLINTA.md`):
   ```
   cd ~/Herd/elainten-hoitola-liperi
   git checkout main
   git pull
   git checkout -b asiakas-<nimi>
   git push -u origin asiakas-<nimi>
   ```
2. Kloonaa TÄMÄ haara omaan, uuteen kansioon Herd-hakemiston alle (ei elainten-hoitola-liperin sisään):
   ```
   cd ~/Herd
   git clone --branch asiakas-<nimi> <repo-url> asiakas-<nimi>
   ```
   Tämä luo täysin erillisen kansion `~/Herd/asiakas-<nimi>/`, jolla on oma `.env`, oma tietokanta, oma Herd-verkko-osoite (esim. `novi.test` uudelleen tässä uudessa kansiossa — jos Herd ei tunnista sitä automaattisesti, avaa Herd-sovellus ja "Park" tämä kansio käsin).
3. **Poista tästä uudesta haarasta ne toimialamoduulit joita asiakas EI osta.** Esim. jos asiakas ostaa vain Lemmikkihoitola-moduulin:
   - Poista kansio `app/Modules/Kurssit/` kokonaan.
   - Poista Kurssit-moduulin ServiceProvider-rekisteröinti (`config/app.php` tai vastaava, ks. `KurssitServiceProvider::class`-rivi).
   - Poista Kurssit-moduulin näkymäkansio `resources/views/modules/kurssit/`.
   - Committaa tämä poisto vain tähän asiakkaan haaraan: `git add -A && git commit -m "Poistettu Kurssit-moduuli, asiakas ei osta sitä" && git push`.
   - `main`-haara pysyy koskemattomana kaikkine moduuleineen — poisto koskee vain tätä yhtä asiakashaaraa.
4. Aja kohdat 1-2 tästä ohjeesta eteenpäin (composer install, .env, migrate, `novi:new-customer`) tässä uudessa kansiossa.

## 1. Koodi ja palvelin
- [ ] Kopioi koodi uuteen kansioon/palvelimelle (oma git-kopio tai `git clone`).
- [ ] Luo uusi, tyhjä tietokanta tälle asiakkaalle (eri tietokanta kuin muilla asiakkailla — ei koskaan jaettu).
- [ ] Kopioi `.env.example` → `.env` ja täytä oikeat tiedot: tietokannan tunnukset, `APP_URL`, sähköpostiasetukset.
- [ ] Lisää `.env`-tiedostoon tämän asiakkaan omat Stripe-avaimet: `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` (nämä EIVÄT saa koskaan olla samat kuin toisella asiakkaalla).
- [ ] Aja: `composer install`
- [ ] Aja: `php artisan key:generate`
- [ ] Aja: `php artisan migrate`

## 2. Yrityksen ja pääkäyttäjän luonti
- [ ] Aja: `php artisan novi:new-customer` ja vastaa kysymyksiin (yrityksen nimi, toimiala, pääkäyttäjän sähköposti/salasana).
- [ ] Kirjaudu sisään uusilla tunnuksilla osoitteessa `/login` ja tarkista että pääsee sisään.

## 3. Brändäys (asiakas voi tehdä tämän itse jatkossa)
- [ ] Asetukset-sivulta: logo, brändivärit, fontti.
- [ ] Tarkista että logo ja värit näkyvät julkisilla sivuilla ja sähköposteissa.

## 4. Maksut
- [ ] Lisää Stripe-webhookin osoite Stripen hallintapaneeliin: `https://<asiakkaan-osoite>/stripe/webhook`.
- [ ] Tee yksi oikea testiosto/varaus ja varmista että maksu ja vahvistussähköposti toimivat.

## 5. Kurssit-moduulin lisäksi (jos käytössä)
- [ ] Tarkista lahjakorttien asetukset (Kurssit → Lahjakortit → Asetukset).
- [ ] Tarkista tietosuojaseloste `/tietosuoja` näyttää oikean yrityksen tiedot.

## 6. Upotus asiakkaan omalle sivustolle (jos julkinen varaus/ilmoittautuminen upotetaan WordPressiin tms.)

Tämä koodi liitetään sille TOISELLE sivustolle (WordPress-sivun "Mukautettu HTML" -lohko tai vastaava tavallisella PHP-sivulla), EI novi-koodikantaan. Vaihda `<sinun-alidomainisi>` oikeaan osoitteeseen ennen käyttöä.

**Kurssit-moduuli:**
```html
<iframe id="novi-kurssit-iframe" src="https://<sinun-alidomainisi>/kurssit" style="width:100%; height:800px; border:0;"></iframe>
<script>
window.addEventListener('message', function (event) {
    if (event.data && event.data.noviIframeHeight) {
        var iframe = document.getElementById('novi-kurssit-iframe');
        if (iframe) {
            iframe.style.height = event.data.noviIframeHeight + 'px';
        }
    }
});
</script>
```

**Lemmikkihoitola-moduuli** (sama periaate, vaihda vain `id` ja `src`):
```html
<iframe id="novi-varaa-iframe" src="https://<sinun-alidomainisi>/varaa" style="width:100%; height:800px; border:0;"></iframe>
<script>
window.addEventListener('message', function (event) {
    if (event.data && event.data.noviIframeHeight) {
        var iframe = document.getElementById('novi-varaa-iframe');
        if (iframe) {
            iframe.style.height = event.data.noviIframeHeight + 'px';
        }
    }
});
</script>
```

Korkeus säätyy automaattisesti sisällön mukaan (edellyttää että novin puolella on korkeudensäätöskripti asennettuna — ks. `tekninen-katsaus-asiantuntijalle.md` kohta 2/11 upotuksesta). Jos skriptiä ei vielä ole asennettu jompaankumpaan moduuliin, `height`-arvo (800px) pysyy kiinteänä eikä säädy.

- [ ] Testaa upotus oikeasti asiakkaan sivustolla ennen julkaisua.

## 7. Versio
- [ ] Kirjaa ylös mikä versio (`config/versions.php`) tälle asiakkaalle asennettiin, jotta tiedät mitä päivityksiä pitää myöhemmin viedä hänelle.

## 8. Muista aina uudessa asennuksessa (tunnetut rajoitukset, ei vielä automatisoitu)
- [ ] **`.env`-tiedostossa `APP_DEBUG` pitää olla `false`** oikeassa tuotantoasennuksessa. Jos se jää `true`:ksi (kuten `.env.example`:n oletus on kehitystä varten), virhetilanteet näyttävät asiakkaalle/asiakkaan käyttäjille teknisiä tietoja (tiedostopolkuja, SQL-lauseita) suoraan selaimessa — tietoturva- ja ammattimaisuusriski.
- [ ] **`php artisan novi:new-customer` -komento kaatuu rumalla virheellä**, jos syötät sähköpostin joka on jo käytössä tässä asennuksessa (esim. jos ajat komennon vahingossa kahdesti). Tämä ei vielä anna siistiä virheilmoitusta — jos näin käy, se ei ole vikaa sinun toiminnassasi, komento vain tarvitsee vielä pienen parannuksen. Jos tämä toistuu usein, pyydä parannus tehtäväksi.
- [ ] **Oman kirjautuneen käyttäjän nimi/sähköposti** (näkyy hallintapaneelin vasemman sivuvalikon alareunassa) tulee aina suoraan siitä käyttäjätunnuksesta jolla olet kirjautunut sisään — se ei ole kovakoodattu mihinkään. Kun uusi asiakas kirjautuu omilla `novi:new-customer`-komennolla luoduilla tunnuksillaan, tuo kohta näyttää automaattisesti HÄNEN nimensä ja sähköpostinsa, ei sinun. Jos oma nimesi näkyy väärin (esim. "testi"), voit vaihtaa sen itse osoitteessa `/profile`.
