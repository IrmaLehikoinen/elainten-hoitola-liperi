<x-layouts.public :step="2" :total-steps="6">
    <div x-data="{
                available: {{ \Illuminate\Support\Js::from(collect($dates)->pluck('iso')) }},
        current: (() => {
            const first = {{ \Illuminate\Support\Js::from(collect($dates)->pluck('iso')->first()) }};
            const d = first ? new Date(first) : new Date();
            return new Date(d.getFullYear(), d.getMonth(), 1);
        })(),
        get monthLabel() {
            return this.current.toLocaleDateString('fi-FI', { month: 'long', year: 'numeric' });
        },
        get weeks() {
            const year = this.current.getFullYear();
            const month = this.current.getMonth();
            const firstDay = new Date(year, month, 1);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const cells = [];
            for (let i = 0; i < startOffset; i++) cells.push(null);
            for (let d = 1; d <= daysInMonth; d++) {
                const iso = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                cells.push({ day: d, iso, isAvailable: this.available.includes(iso) });
            }
            while (cells.length % 7 !== 0) cells.push(null);
            const weeks = [];
            for (let i = 0; i < cells.length; i += 7) weeks.push(cells.slice(i, i + 7));
            return weeks;
        },
        prevMonth() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() - 1, 1); },
        nextMonth() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() + 1, 1); },
        selectDate(iso) {
            if (!this.available.includes(iso)) return;
            document.getElementById('hold-date-input').value = iso;
            document.getElementById('hold-form').submit();
        }
    }">
        <h2 class="text-2xl font-bold" style="font-family: var(--brand-heading-font); color: var(--brand-text);">
            Valitse vapaa aloituspäivä
        </h2>
        <p class="text-sm" style="color: var(--brand-text); opacity: 0.6;">Vihreällä reunalla merkityt päivät ovat vapaita</p>

        <form id="hold-form" method="POST" action="{{ route('public.booking.hold') }}">
            @csrf
            <input type="hidden" id="hold-date-input" name="start_date" value="">
        </form>

        <div class="public-field" style="margin-top: 28px; padding: 20px; border: 1.5px solid #EAE3DC; border-radius: 10px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                <button type="button" @click="prevMonth" style="border:1px solid #EAE3DC; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; cursor:pointer; background:white; color: var(--brand-text);">‹</button>
                <span style="font-family: var(--brand-heading-font); font-weight:700; font-size:15px; color: var(--brand-text); text-transform:capitalize;" x-text="monthLabel"></span>
                <button type="button" @click="nextMonth" style="border:1px solid #EAE3DC; border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center; cursor:pointer; background:white; color: var(--brand-text);">›</button>
            </div>

            <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:6px; text-align:center; font-size:11px; font-weight:600; opacity:0.5; margin-bottom:8px; color: var(--brand-text);">
                <span>Ma</span><span>Ti</span><span>Ke</span><span>To</span><span>Pe</span><span>La</span><span>Su</span>
            </div>

            <template x-for="(week, wi) in weeks" :key="wi">
                <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-bottom:6px;">
                    <template x-for="(cell, ci) in week" :key="ci">
                        <div>
                            <template x-if="cell">
                                <button
                                    type="button"
                                    @click="selectDate(cell.iso)"
                                    :disabled="!cell.isAvailable"
                                    :style="cell.isAvailable
                                        ? 'width:100%; aspect-ratio:1; border-radius:8px; border:1.5px solid var(--brand-primary); background:white; color:var(--brand-text); font-weight:600; font-size:13px; cursor:pointer;'
                                        : 'width:100%; aspect-ratio:1; border-radius:8px; border:1px solid #F0EAE4; background:#FAF8F5; color:#C7C0B8; font-size:13px; cursor:not-allowed;'"
                                    x-text="cell.day"
                                ></button>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <div class="public-field" style="margin-top: 32px;">
            <label class="public-field-label">Tai valitse listalta</label>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse ($dates as $date)
                    <button type="button" @click="selectDate('{{ $date['iso'] }}')" class="public-input" style="text-align:left; cursor:pointer; display:flex; align-items:center;">
                        {{ $date['display'] }}
                    </button>
                @empty
                    <p class="text-sm" style="color: var(--brand-text); opacity: 0.7;">
                        Valitettavasti tälle kokoonpanolle ei löytynyt vapaita aikoja lähitulevaisuudesta. Ota yhteyttä suoraan hoitolaan.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="mt-8">
            <span onclick="window.location.href='{{ route('public.booking.start') }}'" class="cursor-pointer text-sm font-medium" style="color: var(--brand-primary);">
                ← Muuta hakuehtoja
            </span>
        </div>
    </div>
</x-layouts.public>