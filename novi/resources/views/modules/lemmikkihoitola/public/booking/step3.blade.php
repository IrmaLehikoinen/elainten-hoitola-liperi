<x-layouts.public :step="4" :total-steps="6">
    <h2 class="text-2xl font-bold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
        Aika varattu sinulle 10 minuutiksi
    </h2>

    <p class="text-sm" style="color: var(--brand-text); opacity: 0.6;">
        {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
    </p>

    @if (!empty($linkSent))
        <div class="public-field" style="margin-top: 28px; text-align:center; padding: 32px 20px;">
            <p style="font-family: var(--brand-heading-font); font-weight:700; font-size:18px; color: var(--brand-text); margin-bottom:8px;">
                Tarkista sähköpostisi
            </p>
            <p class="text-sm" style="color: var(--brand-text); opacity: 0.7;">
                             Olet jo asiakkaamme. Lähetimme kirjautumislinkin osoitteeseen <strong>{{ $email }}</strong>. Avaa siellä oleva linkki jatkaaksesi varauksen tekemistä. Asiakas- ja lemmikkitietosi ovat valmiina, joten sinun ei tarvitse täyttää niitä uudelleen.   
            </p>
        </div>
    @else
        <div class="public-field" style="margin-top: 28px;">
            <label class="public-field-label">Sähköpostiosoite</label>
            <form method="POST" action="{{ route('public.booking.identify') }}">
                @csrf
                <input type="email" name="email" required placeholder="etunimi.sukunimi@esimerkki.fi" class="public-input">
                <button type="submit" class="btn-brand w-full px-4 py-3 text-sm font-semibold" style="margin-top:16px;">
                    Jatka →
                </button>
            </form>
        </div>
        <p class="text-sm" style="color: var(--brand-text); opacity: 0.5; margin-top:12px;">
            Jos olet asioinut kanssamme aiemmin, lähetämme sinulle kirjautumislinkin eikä sinun tarvitse täyttää tietoja uudelleen.
        </p>
    @endif
</x-layouts.public>