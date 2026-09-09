<x-ajanvaraus::layouts.public title="Varaa aika">
    <style>
                h1 { font-family: var(--brand-heading-font); font-size: 24px; color: var(--brand-text); margin-top: 32px; }
        h2 { font-family: var(--brand-heading-font); font-size: 24px; margin-top: 28px; color: #80107A; }        
        label { display: block; font-size: 13px; color: var(--brand-text); opacity: 0.7; margin-top: 12px; }
        select, input {
            width: 100%; padding: 10px; margin-top: 4px; border: 1px solid var(--brand-secondary);
            border-radius: var(--brand-radius); box-sizing: border-box; font-size: 15px;
            font-family: var(--brand-body-font); color: var(--brand-text); background: #fff;
        }
        .pick-btn {
            margin-top: 24px; padding: 11px 22px; background: var(--brand-primary); color: #fff;
            border: none; border-radius: 999px; font-weight: 600; cursor: pointer; font-family: var(--brand-body-font);
        }
        .slot-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 16px 0; }
        .slot-btn {
            padding: 10px; border: 1px solid var(--brand-secondary); border-radius: var(--brand-radius);
            text-align: center; background: #fff; cursor: pointer; font-size: 14px; color: var(--brand-text);
            font-family: var(--brand-body-font);
        }
        .slot-btn:hover { border-color: var(--brand-primary); }
        
        .error { color: #b3261e; font-size: 13px; }
        .hint { font-size: 13px; color: #b3261e; margin-top: 16px; margin-bottom: 24px; }
        .treatment-desc { margin-top: 14px; }
        .no-slots { margin-top: 28px; }
        .no-slots a { color: var(--brand-accent); text-decoration: underline; }       
        .cal-nav-btn { border: 1px solid var(--brand-secondary); border-radius: 8px; width: 32px; height: 32px; cursor: pointer; background: #fff; color: var(--brand-text); }
        .cal-day-label { font-size: 11px; font-weight: 600; opacity: 0.5; text-align: center; color: var(--brand-text); }
        .upcoming-item {
            display: flex; justify-content: space-between; align-items: center; width: 100%;
            padding: 12px 14px; border: 1px solid var(--brand-secondary); border-radius: var(--brand-radius);
            background: #fff; cursor: pointer; font-family: var(--brand-body-font); font-size: 14px; color: var(--brand-text);
            margin-bottom: 8px;
        }
                .upcoming-item:hover { border-color: var(--brand-primary); }
        .upcoming-item strong { font-weight: 700; }
        .back-link {
            display: inline-flex; align-items: center; gap: 6px; background: none; border: none; padding: 0;
            cursor: pointer; font-family: var(--brand-body-font); font-size: 14px; color: var(--brand-primary);
            font-weight: 600; margin-bottom: 8px;
        }
        .back-link:hover { text-decoration: underline; }

        /* ===== HOIDON VALINTA (custom listbox) ===== */
        .th-select-wrap { position: relative; margin-top: 8px; margin-bottom: 24px; }
        .th-select-trigger {
            width: 100%; max-width: 760px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
            min-height: 56px; padding: 14px 18px; background: #FCFBF8; border: 1px solid var(--brand-secondary);
            border-radius: 15px; font-family: var(--brand-body-font); font-size: 16px; color: var(--brand-text);
            cursor: pointer; text-align: left; box-sizing: border-box;
        }
        .th-select-trigger:focus-visible, .th-select-trigger.is-open {
            outline: none; border-color: var(--brand-primary); box-shadow: 0 0 0 2px rgba(124,171,51,0.18);
        }
        .th-select-trigger-name { display: block; font-weight: 600; line-height: 1.35; }
        .th-select-trigger-meta { display: block; font-size: 13px; opacity: 0.65; margin-top: 2px; }
        .th-select-trigger-placeholder { opacity: 0.55; }
        .th-select-chevron { flex-shrink: 0; transition: transform .15s ease; opacity: 0.6; }
        .th-select-trigger.is-open .th-select-chevron { transform: rotate(180deg); }
        .th-select-panel {
            position: absolute; z-index: 30; top: calc(100% + 6px); left: 0; right: 0; max-width: 760px;
            background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(42,52,40,0.14);
            max-height: 460px; overflow-y: auto; padding: 10px; box-sizing: border-box;
        }
        .th-select-group { padding: 6px 6px 12px; }
        .th-select-group + .th-select-group { border-top: 1px solid var(--brand-secondary); margin-top: 6px; padding-top: 10px; }
                .th-select-group-label { 
            font-family: var(--brand-heading-font); font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
                        text-decoration: underline; text-underline-offset: 5px; text-decoration-thickness: 1.5px;
        }
        .th-select-group:first-child .th-select-group-label { padding-top: 6px; }
        .th-select-option {
            padding: 12px 8px; border-radius: 11px; cursor: pointer; min-height: 50px;
            box-sizing: border-box; display: flex; flex-direction: column; justify-content: center; gap: 2px;
        }
        .th-select-option.is-active { background: #E2EAD2; }
        .th-select-option.is-selected { background: #DCEAC4; border: 1px solid var(--brand-primary); }
        .th-select-option-name { font-size: 15px; font-weight: 600; color: var(--brand-text); line-height: 1.35; }
        .th-select-option-meta { font-size: 13.5px; color: var(--brand-text); opacity: 0.65; }
        @media (max-width: 640px) {
            .th-select-panel { max-height: 60vh; }
        }
    </style>

        <button type="button" class="back-link" onclick="history.back()">← Takaisin palveluihin</button>

    @if ($treatment)
        <h2>{{ $treatment->name }}</h2>
        <p class="treatment-desc">{{ $treatment->short_description }}</p>
    @endif

    <h1>Varaa aika</h1>

    @if ($treatment)
        @if (! $name || ! $email)
            <p class="hint">Täytä ensin nimi ja sähköposti alle, jotta voit varata ajan.</p>
        @endif
    @endif

    <form method="GET" action="{{ route('ajanvaraus.public.book') }}" id="treatmentPickerForm">
                <label>Valitse hoito</label>
        @php
            $grouped = $treatments->groupBy(fn ($t) => $t->category->name ?? 'Muut');
            $treatmentGroups = $grouped->map(fn ($group, $categoryName) => [
                'name' => $categoryName,
                'items' => $group->map(fn ($t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'meta' => $t->duration_minutes.' min · '.($t->price > 0 ? number_format($t->price, 2, ',', ' ').' €' : 'maksuton'),
                ])->values(),
            ])->values();
        @endphp
        <div
            class="th-select-wrap"
            x-data="{
                open: false,
                groups: {{ \Illuminate\Support\Js::from($treatmentGroups) }},
                flat: [],
                activeId: null,
                selectedId: {{ $treatment ? $treatment->id : 'null' }},
                init() {
                    this.flat = this.groups.flatMap(g => g.items);
                    this.activeId = this.selectedId ?? (this.flat[0] ? this.flat[0].id : null);
                },
                get selected() { return this.flat.find(t => t.id === this.selectedId) || null; },
                openPanel() {
                    this.open = true;
                    this.activeId = this.selectedId ?? (this.flat[0] ? this.flat[0].id : null);
                    this.$nextTick(() => this.$refs.panel && this.$refs.panel.focus());
                },
                closePanel(focusTrigger = true) {
                    this.open = false;
                    if (focusTrigger) this.$nextTick(() => this.$refs.trigger && this.$refs.trigger.focus());
                },
                toggle() { this.open ? this.closePanel() : this.openPanel(); },
                moveActive(delta) {
                    const idx = this.flat.findIndex(t => t.id === this.activeId);
                    const next = Math.min(Math.max(idx + delta, 0), this.flat.length - 1);
                    if (this.flat[next]) {
                        this.activeId = this.flat[next].id;
                        this.$nextTick(() => {
                            var el = document.getElementById('th-opt-' + this.activeId);
                            if (el) el.scrollIntoView({ block: 'nearest' });
                        });
                    }
                },
                selectActive() { if (this.activeId != null) this.selectId(this.activeId); },
                selectId(id) {
                    this.selectedId = id;
                    document.getElementById('treatmentIdInput').value = id;
                    this.closePanel(false);
                    document.getElementById('treatmentPickerForm').submit();
                }
            }"
            @click.outside="closePanel(false)"
        >
            <button
                type="button"
                class="th-select-trigger"
                :class="{ 'is-open': open }"
                x-ref="trigger"
                role="combobox"
                aria-haspopup="listbox"
                :aria-expanded="open ? 'true' : 'false'"
                aria-controls="treatmentListbox"
                @click="toggle()"
                @keydown.down.prevent="openPanel()"
                @keydown.up.prevent="openPanel()"
                @keydown.escape="closePanel(false)"
            >
                <span>
                    <template x-if="selected">
                        <span>
                            <span class="th-select-trigger-name" x-text="selected.name"></span>
                            <span class="th-select-trigger-meta" x-text="selected.meta"></span>
                        </span>
                    </template>
                    <template x-if="!selected">
                        <span class="th-select-trigger-placeholder">Valitse palvelu</span>
                    </template>
                </span>
                <svg class="th-select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>

            <div
                x-show="open"
                x-cloak
                x-ref="panel"
                id="treatmentListbox"
                class="th-select-panel"
                role="listbox"
                tabindex="-1"
                :aria-activedescendant="activeId ? ('th-opt-' + activeId) : null"
                @keydown.down.prevent="moveActive(1)"
                @keydown.up.prevent="moveActive(-1)"
                @keydown.home.prevent="moveActive(-9999)"
                @keydown.end.prevent="moveActive(9999)"
                @keydown.enter.prevent="selectActive()"
                @keydown.space.prevent="selectActive()"
                @keydown.escape.prevent="closePanel()"
                @keydown.tab="closePanel(false)"
            >
                    <template x-for="group in groups" :key="group.name">    
                    <div class="th-select-group" :class="{ 'th-group-alt': gi % 2 === 1 }">
                        <div class="th-select-group-label" x-text="group.name"></div>    
                        <template x-for="t in group.items" :key="t.id">
                            <div
                                :id="'th-opt-' + t.id"
                                class="th-select-option"
                                :class="{ 'is-active': activeId === t.id, 'is-selected': selectedId === t.id }"
                                role="option"
                                :aria-selected="selectedId === t.id ? 'true' : 'false'"
                                @click="selectId(t.id)"
                                @mouseenter="activeId = t.id"
                            >
                                <span class="th-select-option-name" x-text="t.name"></span>
                                <span class="th-select-option-meta" x-text="t.meta"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
        <input type="hidden" name="treatment_id" id="treatmentIdInput" value="{{ $treatment->id ?? '' }}">

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
        <form method="POST" action="{{ route('ajanvaraus.public.store') }}" id="bookForm">
            @csrf
            <input type="hidden" name="treatment_id" value="{{ $treatment->id }}">
            <input type="hidden" name="name" value="{{ $name }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="phone" value="{{ $phone }}">
            <input type="hidden" name="starts_at" id="startsAtInput" value="">
        </form>

        @if (empty($dateSlots))
            <p class="no-slots">Ei vapaita aikoja lähiaikoina. <a href="{{ route('sydanpolku.index') }}#yhteystiedot">Ota yhteyttä suoraan hoitolaan.</a></p>
        @else
            <div
                x-data="{
                    dateSlots: {{ \Illuminate\Support\Js::from($dateSlots) }},
                    current: (() => {
                        const keys = Object.keys({{ \Illuminate\Support\Js::from($dateSlots) }});
                        const first = keys[0];
                        const d = first ? new Date(first + 'T00:00:00') : new Date();
                        return new Date(d.getFullYear(), d.getMonth(), 1);
                    })(),
                    openDate: null,
                    get available() { return Object.keys(this.dateSlots); },
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
                    openDay(iso) {
                        if (!this.available.includes(iso)) return;
                        this.openDate = iso;
                    },
                    closeCard() { this.openDate = null; },
                    dateLabel(iso) {
                        return new Date(iso + 'T00:00:00').toLocaleDateString('fi-FI', { weekday: 'long', day: 'numeric', month: 'numeric' });
                    },
                    pick(iso) {
                        document.getElementById('startsAtInput').value = iso;
                        document.getElementById('bookForm').requestSubmit();
                    }
                }"
                style="position: relative; margin-top: 20px;"
            >
                <div style="padding: 20px; border: 1.5px solid var(--brand-secondary); border-radius: var(--brand-radius);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                        <button type="button" @click="prevMonth" class="cal-nav-btn">‹</button>
                        <span style="font-family: var(--brand-heading-font); font-weight:700; font-size:15px; color: var(--brand-text); text-transform:capitalize;" x-text="monthLabel"></span>
                        <button type="button" @click="nextMonth" class="cal-nav-btn">›</button>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-bottom:8px;">
                        <span class="cal-day-label">Ma</span><span class="cal-day-label">Ti</span><span class="cal-day-label">Ke</span>
                        <span class="cal-day-label">To</span><span class="cal-day-label">Pe</span><span class="cal-day-label">La</span><span class="cal-day-label">Su</span>
                    </div>

                    <template x-for="(week, wi) in weeks" :key="wi">
                        <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-bottom:6px;">
                            <template x-for="(cell, ci) in week" :key="ci">
                                <div>
                                    <template x-if="cell">
                                        <button
                                            type="button"
                                            @click="openDay(cell.iso)"
                                            :disabled="!cell.isAvailable"
                                            :style="cell.iso === openDate
                                                ? 'width:100%; aspect-ratio:1; border-radius:8px; border:1.5px solid var(--brand-primary); background:var(--brand-primary); color:#fff; font-weight:700; font-size:13px; cursor:pointer;'
                                                : (cell.isAvailable
                                                    ? 'width:100%; aspect-ratio:1; border-radius:8px; border:1.5px solid var(--brand-primary); background:#fff; color:var(--brand-text); font-weight:600; font-size:13px; cursor:pointer;'
                                                    : 'width:100%; aspect-ratio:1; border-radius:8px; border:1px solid var(--brand-secondary); background:#FAF8F5; color:#C7C0B8; font-size:13px; cursor:not-allowed;')"
                                            x-text="cell.day"
                                        ></button>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                                <div
                    x-show="openDate"
                    @click.self="closeCard()"
                    style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.35); z-index:50;"
                    x-cloak
                >
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; border-radius: var(--brand-radius); padding:28px; max-width:360px; width:calc(100% - 40px); max-height:80vh; overflow-y:auto;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                            <strong x-text="openDate ? dateLabel(openDate) : ''" style="font-family: var(--brand-heading-font); color: var(--brand-text); text-transform:capitalize;"></strong>
                            <button type="button" @click="closeCard()" style="border:none;background:none;font-size:22px;cursor:pointer;color:var(--brand-text); line-height:1;">×</button>
                        </div>
                        <div class="slot-grid">
                            <template x-for="slot in (dateSlots[openDate] || [])" :key="slot.iso">
                                <button type="button" class="slot-btn" @click="pick(slot.iso)" x-text="slot.label"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            @if (! empty($upcomingSlots))
                <h2>Seuraavat vapaat ajat</h2>
                <div>
                    @foreach ($upcomingSlots as $slot)
                        <button type="button" class="upcoming-item" onclick="document.getElementById('startsAtInput').value='{{ $slot['iso'] }}'; document.getElementById('bookForm').requestSubmit();">
                            <span>{{ $slot['dateLabel'] }}</span>
                            <strong>{{ $slot['label'] }}</strong>
                        </button>
                    @endforeach
                </div>
            @endif
                @endif
    @endif

    <p style="margin-top: 32px; text-align: center; font-size: 12px; opacity: 0.6;">
        <button type="button" onclick="window.open('{{ route('legal.privacy') }}', '_blank')" style="background: none; border: none; cursor: pointer; color: var(--brand-text); text-decoration: underline; font-family: var(--brand-body-font); font-size: 12px;">
            Tietosuojaseloste
        </button>
    </p>
    <p style="margin-top: 8px; text-align: center; font-size: 12px; font-weight: 600; opacity: 0.6;">
        Powered by Novi
    </p>
    <p style="margin-top: 2px; text-align: center; font-size: 10px; font-weight: 300; opacity: 0.4;">
        ajanvaraus &amp; asiakashallinta
    </p>

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