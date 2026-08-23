@props(['step' => null, 'totalSteps' => 6, 'stepLabels' => ['Palvelu', 'Aika', 'Tunnistus', 'Tiedot', 'Varausmaksun maksaminen', 'Valmis']])

<div class="novi-booking-widget">
    <style>
        .novi-booking-widget {
            --brand-primary: {{ $brand['primary_color'] ?? '#3F4F3A' }};
            --brand-secondary: {{ $brand['secondary_color'] ?? '#D8C6BD' }};
            --brand-accent: {{ $brand['accent_color'] ?? '#2A3428' }};
            --brand-background: {{ $brand['background_color'] ?? '#F8F6F2' }};
            --brand-text: {{ $brand['text_color'] ?? '#2A3428' }};
            --brand-heading-font: '{{ $brand['font_heading'] ?? 'Playfair Display' }}', serif;
            --brand-body-font: '{{ $brand['font_body'] ?? 'Inter' }}', sans-serif;
            --brand-radius: {{ $brand['border_radius'] ?? '16px' }};
            margin: 0;
            font-family: var(--brand-body-font);
            color: var(--brand-text);
            background-color: var(--brand-background);
        }

        .novi-booking-widget .public-input {
            width: 100%;
            height: 51px;
            background: white;
            border: 1.5px solid #EAE3DC;
            border-radius: 8px;
            box-shadow: none;
            padding: 0 16px;
            font-size: 14px;
            color: #3A3A3A;
            font-family: var(--brand-body-font);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .novi-booking-widget .public-input:focus {
            outline: none;
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(63,79,58,0.10);
        }

        .novi-booking-widget select.public-input {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none' stroke='%23A3998C' stroke-width='1.6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m5 7.5 5 5 5-5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 13px;
            padding-right: 38px;
        }

        .novi-booking-widget .public-field {
            margin-bottom: 0;
        }

        .novi-booking-widget .public-field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--brand-text);
            margin-bottom: 7px;
        }

        .novi-booking-widget .public-wrap {
            max-width: 760px;
            margin: 0 auto;
            padding: 48px 20px 80px;
        }

        .novi-booking-widget .public-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--brand-accent);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .novi-booking-widget .public-heading {
            font-family: var(--brand-heading-font);
            font-weight: 700;
            font-size: 32px;
            margin: 18px 0 4px;
            color: var(--brand-text);
        }

        .novi-booking-widget .public-subheading {
            color: var(--brand-accent);
            opacity: 0.7;
            font-size: 15px;
            margin: 0;
        }

        .novi-booking-widget .public-stepper {
            display: flex;
            align-items: flex-start;
            margin-top: 32px;
        }

        .novi-booking-widget .public-stepper-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .novi-booking-widget .public-stepper-circle {
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
        }

        .novi-booking-widget .public-stepper-number {
            position: absolute;
            top: 52%;
            left: 39%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            font-weight: 700;
            font-family: var(--brand-body-font);
        }

        .novi-booking-widget .public-stepper-label {
            font-size: 11px;
            color: var(--brand-accent);
            opacity: 0.7;
            white-space: normal;
            max-width: 90px;
            text-align: center;
            line-height: 1.3;
        }

        .novi-booking-widget .public-stepper-line {
            flex: 1;
            height: 2px;
            background: var(--brand-secondary);
            margin: 49px 6px 0;
            min-width: 12px;
        }

        .novi-booking-widget .public-stepper-line.is-done {
            background: var(--brand-primary);
        }

        .novi-booking-widget .public-card {
            margin-top: 28px;
            background: white;
            border-radius: var(--brand-radius);
            padding: 48px 52px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03), 0 12px 32px -20px rgba(0,0,0,0.10);
            border: 1px solid rgba(42,52,40,0.08);
        }

        .novi-booking-widget .public-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: var(--brand-radius);
            background: var(--brand-background);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .novi-booking-widget .public-form-heading-rule {
            width: 46px;
            height: 3px;
            background: var(--brand-primary);
            border-radius: 2px;
            margin: 14px 0 32px;
        }

        .novi-booking-widget .public-pet-heading {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            color: var(--brand-text);
            font-family: var(--brand-heading-font);
        }

        .novi-booking-widget .public-pet-divider {
            height: 2px;
            width: 40px;
            background: var(--brand-secondary);
            border-radius: 2px;
            margin: 8px 0 16px;
        }

        .novi-booking-widget .public-brand-strip {
            margin-top: 32px;
            padding: 18px 16px 0;
            text-align: center;
            border-top: 1px solid var(--brand-secondary);
        }

        .novi-booking-widget .public-brand-strip p {
            margin: 0;
            font-family: var(--brand-heading-font);
            font-style: italic;
            font-size: 14px;
            color: var(--brand-text);
            opacity: 0.7;
        }

        .novi-booking-widget .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            border: none;
            border-radius: var(--brand-radius);
            cursor: pointer;
            font-family: var(--brand-body-font);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .novi-booking-widget .btn-brand:hover {
            filter: brightness(0.95);
        }
    </style>

    <div class="public-wrap">
        <div style="text-align:center;">
            <span class="public-eyebrow">🐾 Lemmikkihoitolan ajanvaraus</span>
            <h1 class="public-heading">{{ $brand['name'] ?? 'Ajanvaraus' }}</h1>
            <p class="public-subheading">Varaa hoitoaika lemmikillesi muutamassa minuutissa</p>

            @isset($step)
                <div class="public-stepper">
                    @foreach ($stepLabels as $i => $label)
                        @if ($i > 0)
                            <div class="public-stepper-line {{ $i < $step ? 'is-done' : '' }}"></div>
                        @endif
                        <div class="public-stepper-item">
                            <div class="public-stepper-circle">
                                @php $isDone = ($i + 1) <= $step; @endphp
                                <svg width="90" height="90" viewBox="0 0 24 24">
                                    <g transform="rotate(90 12 12.5)"
                                       fill="{{ $isDone ? 'var(--brand-primary)' : 'white' }}"
                                       stroke="var(--brand-primary)"
                                       stroke-width="{{ $isDone ? 0 : 0.7 }}"
                                    >
                                        <circle cx="7.5" cy="9" r="2.1"/>
                                        <circle cx="12" cy="6.8" r="2.1"/>
                                        <circle cx="16.5" cy="9" r="2.1"/>
                                        <ellipse cx="12" cy="15.5" rx="5.5" ry="4.5"/>
                                    </g>
                                </svg>
                                <span class="public-stepper-number" style="color: {{ $isDone ? 'white' : 'var(--brand-primary)' }};">
                                    {{ $i + 1 }}
                                </span>
                            </div>
                            <div class="public-stepper-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            @endisset
        </div>

                <div class="public-card">
            {{ $slot }}
        </div>

        <p style="margin-top: 20px; text-align: center; font-size: 12px; opacity: 0.6;">
            <button type="button" onclick="window.open('{{ route('legal.privacy') }}', '_blank')" style="background: none; border: none; cursor: pointer; color: var(--brand-text); text-decoration: underline; font-family: var(--brand-body-font); font-size: 12px;">
                Tietosuojaseloste
            </button>
        </p>

        @isset($footer)
            <div style="margin-top: 64px;">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>