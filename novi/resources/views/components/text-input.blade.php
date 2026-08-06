@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'border-gray-300 rounded-md shadow-sm focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]'
    ]) }}
>