@include('partials.emails.header')

<h1 style="font-size: 20px;">Muistathan varausmaksun tänään</h1>

<p>Hei {{ $booking->customer->name ?? '' }},</p>

<p>Varauksesi ajalle {{ $booking->arrival_at?->format('d.m.Y') }} – {{ $booking->pickup_at?->format('d.m.Y') }} odottaa vielä varausmaksua. Muistathan hoitaa maksun tämän päivän aikana — muuten varaus vapautuu automaattisesti tänä iltana.</p>

<p style="margin-top: 24px; color: #6b7280; font-size: 13px;">
    Jos olet jo maksanut, voit jättää tämän viestin huomiotta.
</p>

@include('partials.emails.footer')