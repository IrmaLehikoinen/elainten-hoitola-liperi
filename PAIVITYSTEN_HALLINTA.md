# Ydinpäivitysten ja asiakaskohtaisten muutosten hallinta

Tämä on turvaverkko sille tilanteelle: teet jonkun asiakkaan koodiin erikoistoiveen mukaisen muutoksen, ja myöhemmin parannat ydintä (pohjaa tai jotain moduulia) — miten varmistat ettei kumpikaan katoa toista päivitettäessä?

## Perusidea: git-branchit per asiakas

`main`-haara (GitHubissa) on aina se **myytävä perustuote** — pohja + moduulit, ei kenenkään yksittäisen asiakkaan erikoistoiveita. Kun toimitat järjestelmän asiakkaalle:

1. Luo asiakkaalle oma haara `main`:sta, esim:
   ```
   git checkout main
   git pull
   git checkout -b asiakas-vaahterahieronta
   git push -u origin asiakas-vaahterahieronta
   ```
2. **Asiakkaan palvelimelle asennetaan aina heidän oma haaransa**, ei `main`.
3. Jos teet jotain vain tälle asiakkaalle (erikoistoive, jota muut eivät saa), commitoi se **vain tähän haaraan**, ei koskaan `main`-haaraan.

## Kun parannat ydintä (bugikorjaus, uusi ominaisuus kaikille)

Tee ja committaa muutos normaalisti `main`-haaraan (kuten tänään tehtiin Kurssit-moduulin kanssa). Kun haluat viedä tämän saman parannuksen jollekin asiakkaalle:

```
git checkout asiakas-vaahterahieronta
git merge main
```

Git yhdistää muutokset automaattisesti niissä tiedostoissa, joita et ole koskenut asiakaskohtaisesti. Jos jokin tiedosto on sekä ydinpäivityksessä että asiakkaan omassa muutoksessa, git pysäyttää ja näyttää täsmälleen ne rivit jotka ovat ristiriidassa ("merge conflict") — tämä on juuri se turvaverkko: git ei koskaan hiljaa hukkaa kumpaakaan muutosta, se pakottaa sinut valitsemaan/yhdistämään rivi riviltä ennen kuin voit jatkaa. Jos konflikteja ei tule, `merge` menee läpi automaattisesti eikä mitään voi kadota.

Kun `merge` on tehty (ja mahdolliset konfliktit ratkaistu), push:
```
git push
```
ja päivitä asiakkaan palvelimella koodi (`git pull` siellä, tai vastaava käyttöönottotapa).

## Nyrkkisääntö: pidä asiakaskohtaiset erot mahdollisimman pieninä

Aina kun mahdollista, tee asiakaskohtainen erikoisuus **asetuksena koodin sijaan** — esim. `Company.settings`-kenttään tietokantaan (kuten brändivärit, lahjakorttien voimassaoloaika jne. jo toimivat). Asetukset eivät koskaan aiheuta merge-konflikteja, koska ne eivät ole koodissa ollenkaan. Vasta jos asiakas oikeasti tarvitsee toisenlaista KOODIA (ei vain toisenlaista asetusta), käytä branch-mallia yllä.

## Muista aina tarkistaa mergen jälkeen

Ennen kuin viet mergetyn koodin asiakkaan tuotantopalvelimelle: testaa se ensin (paikallisesti tai testiympäristössä), täsmälleen samaan tapaan kuin tänään testattiin `/varaa` ja `/kurssit` -sivut korjauksen jälkeen.

## Pidä kirjaa: mikä asiakas, mikä haara, mikä versio

Yksinkertainen taulukko (päivitä tähän manuaalisesti aina kun teet jotain):

| Asiakas | Git-haara | Viimeksi mergetty `main`-commit | Huomiot |
|---|---|---|---|
| (oma tuotanto: Testihoitola + Sydänpolku) | `main` | — | Ei erillistä haaraa, tämä ON `main` |
