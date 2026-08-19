<x-layouts.public :company="$company">
    <h2 class="text-lg font-semibold" style="color: var(--brand-text);">
        Aika varattu sinulle 10 minuutiksi
    </h2>

    <p class="mt-3 text-sm text-gray-600">
        {{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}
    </p>

    <p class="mt-4 rounded-md bg-gray-50 p-4 text-sm text-gray-500">
        Seuraavaksi rakennetaan tähän: sähköpostin kysyminen, taikalinkki palaaville asiakkaille, ja tietojen täyttö. Tämä sivu on tilapäinen paikkamerkki.
    </p>
</x-layouts.public>