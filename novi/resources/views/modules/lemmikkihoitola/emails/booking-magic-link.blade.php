@include('partials.emails.header')

<h1 style="font-size: 20px;">Jatka ajanvaraustasi</h1>

<p>Hei {{ $customer->name }},</p>

<p>Klikkaa alla olevaa linkkiä jatkaaksesi ajanvarausta. Tietosi täyttyvät automaattisesti valmiiksi.</p>

<p style="margin-top: 20px;">
    <a href="{{ $signedUrl }}" style="display:inline-block; background:#3F4F3A; color:white; padding:12px 24px; border-radius:6px; text-decoration:none;">
        Jatka varausta
    </a>
</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Linkki on voimassa 30 minuuttia. Jos et tehnyt tätä varausta, voit jättää tämän viestin huomiotta.
</p>

@include('partials.emails.footer')