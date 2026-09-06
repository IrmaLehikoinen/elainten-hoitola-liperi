<x-ajanvaraus::layouts.public title="Kiitos varauksesta">
    <h1 style="font-family: var(--brand-heading-font); color: var(--brand-text);">Kiitos varauksestasi!</h1>
    <p>{{ $appointment->treatment->name }} — {{ $appointment->starts_at->translatedFormat('l j.n.Y \k\l\o H:i') }}</p>
    <p>Vahvistus on lähetetty osoitteeseen {{ $appointment->email }}.</p>
</x-ajanvaraus::layouts.public>