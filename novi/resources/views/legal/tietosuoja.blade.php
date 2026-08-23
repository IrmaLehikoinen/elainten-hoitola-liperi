<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tietosuojaseloste — {{ $brand['name'] ?? 'Ajanvaraus' }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #2A3428;
            background-color: #F8F6F2;
            max-width: 720px;
            margin: 0 auto;
            padding: 48px 24px 80px;
            line-height: 1.6;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            margin-bottom: 8px;
        }
        h2 {
            font-size: 17px;
            margin-top: 32px;
            margin-bottom: 8px;
        }
        p, li { font-size: 15px; }
    </style>
</head>
<body>
    <h1>Tietosuojaseloste</h1>
    <p style="color:#666; font-size: 13px;">Päivitetty {{ now()->format('d.m.Y') }}</p>

    <h2>1. Rekisterinpitäjä</h2>
    <p>
        {{ $companyRecord->settings['official_name'] ?? $companyRecord->name ?? '[Yrityksen nimi]' }}<br>
        @if (!empty($companyRecord->settings['business_id']))
            Y-tunnus: {{ $companyRecord->settings['business_id'] }}<br>
        @endif
        @if (!empty($companyRecord->settings['address']))
            {{ $companyRecord->settings['address'] }}<br>
        @endif
        @if ($companyRecord->email)
            {{ $companyRecord->email }}<br>
        @endif
        @if ($companyRecord->phone)
            {{ $companyRecord->phone }}
        @endif
    </p>

    <h2>2. Yhteyshenkilö rekisteriä koskevissa asioissa</h2>
    <p>
        @if ($companyRecord->email){{ $companyRecord->email }}@endif
        @if ($companyRecord->phone) · {{ $companyRecord->phone }}@endif
    </p>

    <h2>3. Rekisterin nimi</h2>
    <p>{{ $companyRecord->settings['official_name'] ?? $companyRecord->name ?? 'Yrityksen' }} asiakas- ja varausrekisteri</p>

    <h2>4. Henkilötietojen käsittelyn tarkoitus ja oikeusperuste</h2>
    <p>Henkilötietoja käsitellään lemmikkihoitopalvelun varaamiseksi ja toteuttamiseksi, asiakassuhteen hoitamiseksi sekä laskutusta varten. Käsittelyn oikeusperusteena on asiakkaan ja rekisterinpitäjän välinen sopimus (varaus/hoitopalvelu) sekä lakisääteinen velvoite (kirjanpitolaki, laskutustietojen säilytys).</p>

    <h2>5. Rekisterin tietosisältö</h2>
    <ul>
        <li>Asiakkaan nimi, puhelinnumero, sähköpostiosoite, osoite</li>
        <li>Lemmikin tiedot: nimi, laji, rotu, syntymäaika, sukupuoli, paino, mikrosirunumero, rokotustiedot, allergiat, lääkitykset, ruokintaohjeet, käytöstiedot, eläinlääkärin yhteystiedot, hätätilanneohjeet</li>
        <li>Varaustiedot: ajankohdat, palvelut, hinnat</li>
        <li>Laskutustiedot (ei maksukorttitietoja — maksut käsittelee Stripe, ks. kohta 7)</li>
    </ul>

    <h2>6. Säännönmukaiset tietolähteet</h2>
    <p>Tiedot saadaan asiakkaalta itseltään varauksen yhteydessä (verkkolomake tai henkilökunnan kirjaamana).</p>

    <h2>7. Tietojen luovutukset ja käsittelijät</h2>
    <p>Maksujen käsittelyä varten tietoja (nimi, sähköposti, maksusumma) välitetään maksunvälittäjä Stripelle (Stripe, Inc.). Tietoja ei luovuteta muille kolmansille osapuolille ilman lakisääteistä perustetta.</p>

    <h2>8. Tietojen säilytysaika</h2>
    <p>Asiakas- ja lemmikkitietoja säilytetään niin kauan kuin asiakassuhde on voimassa. Laskutustiedot säilytetään kirjanpitolain edellyttämät 6 vuotta.</p>

    <h2>9. Rekisterin suojauksen periaatteet</h2>
    <p>Tiedot säilytetään sähköisesti pääsynhallinnalla suojatussa järjestelmässä. Vain rekisterinpitäjän henkilökunnalla, joilla on työtehtävän edellyttämä tarve, on pääsy tietoihin.</p>

    <h2>10. Tarkastusoikeus ja oikeus vaatia tiedon korjaamista</h2>
    <p>Asiakkaalla on oikeus tarkastaa itseään koskevat tiedot ja vaatia virheellisen tiedon korjaamista ottamalla yhteyttä yllä mainittuun yhteyshenkilöön.</p>

    <h2>11. Muut henkilötietojen käsittelyyn liittyvät oikeudet</h2>
    <p>Asiakkaalla on oikeus pyytää henkilötietojensa poistamista siltä osin kuin lakisääteiset säilytysvelvoitteet eivät sitä estä, sekä oikeus tehdä valitus valvontaviranomaiselle (Tietosuojavaltuutetun toimisto).</p>
</body>
</html>