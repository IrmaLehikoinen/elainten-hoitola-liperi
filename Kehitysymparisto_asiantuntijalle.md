# Novi-varausjärjestelmä – kehitysympäristön pystytysohje

## Ennen kuin aloitat: pääsy koodiin

Irma lisää sinut GitHub-repositorion yhteistyökumppaniksi osoitteessa
`https://github.com/IrmaLehikoinen/novi-varausjarjestelma` (Settings → Collaborators and teams →
Add people). Hyväksy kutsu sähköpostista ennen kuin jatkat alla oleviin vaiheisiin.

## 1. Asenna tarvittavat työkalut

- **Laravel Herd** (asentaa samalla PHP:n): https://herd.laravel.com
- **Composer** (PHP:n pakettienhallinta): https://getcomposer.org
- **Node.js** (npm tulee mukana): https://nodejs.org

## 2. Kloonaa projekti

```bash
cd ~/Herd
git clone https://github.com/IrmaLehikoinen/novi-varausjarjestelma.git novi
cd novi
```

## 3. Asenna riippuvuudet

```bash
composer install
npm install
npm run build
```

## 4. Ympäristötiedosto

```bash
cp .env.example .env
php artisan key:generate
```

Pyydä Irmalta erikseen (ei koskaan Gitin kautta, koska nämä ovat salaisia tietoja) seuraavat
arvot `.env`-tiedostoon:

- Tietokannan tunnukset (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`)
- Stripe-avaimet (`STRIPE_KEY`, `STRIPE_SECRET`, webhook-salaisuus)
- Sähköpostiasetukset (`MAIL_*`)

## 5. Tietokanta

Luo paikallinen MySQL-tietokanta, jonka nimi täsmää `.env`-tiedoston `DB_DATABASE`-arvoon
(esim. `novi_varausjarjestelma`). Tämän voi tehdä esim. Herdin omalla tietokantatyökalulla tai
`mysql`-komentorivillä.

Aja sitten migraatiot, jotka luovat kaikki taulut:

```bash
php artisan migrate
```

## 6. Linkitä Herdiin

```bash
herd link novi
```

Sivusto aukeaa tämän jälkeen osoitteesta `http://novi.test`.

## 7. Kirjaudu sisään

Käytä olemassa olevaa käyttäjätunnusta, tai luo uusi testikäyttäjä tarvittaessa:

```bash
php artisan tinker
```

## Muista

- `novi`-kansioon EI muokata mitään tiedostoja suoraan ilman katselmointia — kaikki muutokset
  käydään läpi ja testataan huolellisesti ennen tallennusta.
- `.env`-tiedosto ei koskaan mene Gitiin (se on `.gitignore`-listalla) — salaisuudet jaetaan aina
  erikseen, turvallista kanavaa pitkin.
