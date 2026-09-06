<x-ajanvaraus::layouts.public title="Varaa aika">
    <style>
        h1 { font-family: var(--brand-heading-font); font-size: 24px; color: var(--brand-text); margin-top: 0; }
        h2 { font-family: var(--brand-heading-font); font-size: 19px; margin-top: 28px; color: var(--brand-text); }
        label { display: block; font-size: 13px; color: var(--brand-text); opacity: 0.7; margin-top: 12px; }
        select, input {
            width: 100%; padding: 10px; margin-top: 4px; border: 1px solid var(--brand-secondary);
            border-radius: var(--brand-radius); box-sizing: border-box; font-size: 15px;
            font-family: var(--brand-body-font); color: var(--brand-text); background: #fff;
        }
        .pick-btn {
            margin-top: 16px; padding: 11px 22px; background: var(--brand-primary); color: #fff;
            border: none; border-radius: 999px; font-weight: 600; cursor: pointer; font-family: var(--brand-body-font);
        }
        .slot-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 16px 0; }
        .slot-btn {
            padding: 10px; border: 1px solid var(--brand-secondary); border-radius: var(--brand-radius);
            text-align: center; background: #fff; cursor: pointer; font-size: 14px; color: var(--brand-text);
            font-family: var(--brand-body-font);
        }
        .slot-btn:hover { border-color: var(--brand-primary); }
        .day-nav { display: flex; justify-content: space-between; align-items: center; margin: 16px 0; font-size: 14px; }
        .day-nav a { text-decoration: none; color: var(--brand-primary); font-weight: 600; }
        .error { color: #b3261e; font-size: 13px; }
        .hint { font-size: 13px; color: #b3261e; }
    </style>

    <h1>Varaa aika</h1>

    <form method="GET" action="{{ route('ajanvaraus.public.book') }}">
                <label>Valitse hoito</label>
        <select name="treatment_id" onchange="this.form.submit()">
            <option value="">— valitse —</option>
            @php $grouped = $treatments->groupBy(fn ($t) => $t->category->name ?? 'Muut'); @endphp
            @foreach ($grouped as $categoryName => $group)
                <optgroup label="{{ $categoryName }}">
                    @foreach ($group as $t)
                        <option value="{{ $t->id }}" @selected($treatment && $treatment->id === $t->id)>
                            {{ $t->name }} · {{ $t->duration_minutes }} min · {{ $t->price > 0 ? number_format($t->price, 2, ',', ' ').' €' : 'maksuton' }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>

        <label>Nimi</label>
        <input type="text" name="name" id="pickerName" value="{{ $name }}">

        <label>Sähköposti</label>
        <input type="email" name="email" id="pickerEmail" value="{{ $email }}">

        <label>Puhelin</label>
        <input type="text" name="phone" value="{{ $phone }}">

        <button type="submit" class="pick-btn">Näytä vapaat ajat</button>
    </form>

    @error('starts_at')
        <p class="error">{{ $message }}</p>
    @enderror

    @if ($treatment)
        <h2>{{ $treatment->name }}</h2>
        <p>{{ $treatment->short_description }}</p>

        @php
            $baseQuery = ['treatment_id' => $treatment->id, 'name' => $name, 'email' => $email, 'phone' => $phone];
        @endphp

        <div class="day-nav">
            <a href="{{ route('ajanvaraus.public.book', $baseQuery + ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}">← Edellinen päivä</a>
            <strong>{{ $date->translatedFormat('l j.n.Y') }}</strong>
            <a href="{{ route('ajanvaraus.public.book', $baseQuery + ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}">Seuraava →</a>
        </div>

        @if (empty($slots))
            <p>Ei vapaita aikoja tälle päivälle. Kokeile toista päivää.</p>
        @else
            @if (! $name || ! $email)
                <p class="hint">Täytä ensin nimi ja sähköposti yllä, jotta voit varata ajan.</p>
            @endif

            <form method="POST" action="{{ route('ajanvaraus.public.store') }}" id="bookForm">
                @csrf
                <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
                <input type="hidden" name="name" value="{{ $name }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="phone" value="{{ $phone }}">

                <div class="slot-grid">
                    @foreach ($slots as $slot)
                        <button type="submit" name="starts_at" value="{{ $slot['start']->toDateTimeString() }}" class="slot-btn">
                            {{ $slot['start']->format('H:i') }}
                        </button>
                    @endforeach
                </div>
            </form>
        @endif
    @endif

    <script>
        var form = document.getElementById('bookForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                var name = document.getElementById('pickerName').value.trim();
                var email = document.getElementById('pickerEmail').value.trim();
                if (!name || !email) {
                    e.preventDefault();
                    alert('Täytä ensin nimi ja sähköposti.');
                }
            });
        }
    </script>
</x-ajanvaraus::layouts.public>