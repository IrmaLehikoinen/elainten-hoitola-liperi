<x-mail::message>
# Varauksesi on vahvistettu

Hei {{ $booking->customer->name }},

Varauksesi #{{ $booking->id }} on vahvistettu ja varausmaksu on maksettu onnistuneesti.

**Hoitojakso:** {{ $booking->start_date }} – {{ $booking->end_date }}
**Varausmaksu:** {{ $booking->deposit_amount }} €

Kiitos varauksestasi!

Terveisin,<br>
{{ config('app.name') }}
</x-mail::message>