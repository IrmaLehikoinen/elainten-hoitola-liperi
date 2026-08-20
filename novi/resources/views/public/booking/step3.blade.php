<x-layouts.public :step="3" :total-steps="5">
    <h2 class="text-xl font-semibold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
        Aika varattu sinulle 10 minuutiksi
    </h2>

    <p class="mt-3 text-sm" style="color: var(--brand-text); opacity: 0.8;">
        {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
    </p>

    <p class="mt-4 rounded-lg p-4 text-sm" style="background-color: var(--brand-background); color: var(--brand-text); opacity: 0.7;">
        Seuraavaksi rakennetaan tähän: sähköpostin kysyminen, taikalinkki palaaville asiakkaille, ja tietojen täyttö. Tämä sivu on tilapäinen paikkamerkki.
    </p>
</x-layouts.public>