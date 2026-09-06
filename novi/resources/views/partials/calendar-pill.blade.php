@php
    $pillFilled = $filled ?? true;
    $pillColor = $color ?? '#999999';
    $textColor = $pillColor;

    if (str_starts_with($pillColor, '#') && strlen(ltrim($pillColor, '#')) === 6) {
        $hex = ltrim($pillColor, '#');
        $rr = hexdec(substr($hex, 0, 2)); $gg = hexdec(substr($hex, 2, 2)); $bb = hexdec(substr($hex, 4, 2));
        $brightness = (($rr * 299) + ($gg * 587) + ($bb * 114)) / 1000;
        $textColor = $brightness > 180 ? '#111' : $pillColor;
    }
@endphp

@if ($pillFilled)
    <div class="rounded px-1.5 py-1 text-[9px] leading-snug break-words text-white" style="background-color: {{ $pillColor }};">{{ $title }}</div>
@else
    <div class="rounded px-1.5 py-1 text-[9px] leading-snug break-words border" style="border-color: {{ $pillColor }}; color: {{ $textColor }};">{{ $title }}</div>
@endif