@if(!empty($brand['logo']))
    <img
        src="{{ asset('logos/' . $brand['logo']) }}"
        alt="{{ $company['name'] ?? 'Logo' }}"
        {{ $attributes->merge(['class' => 'h-12 w-auto object-contain']) }}
    >
@else
    <div
        {{ $attributes->merge(['class' => 'flex h-12 w-12 items-center justify-center rounded-lg']) }}
        style="
            background: var(--brand-primary);
            color: white;
            font-weight: 700;
        "
    >
        LOGO
    </div>
@endif