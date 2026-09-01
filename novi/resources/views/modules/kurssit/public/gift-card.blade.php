<x-kurssit::layouts.public title="Osta lahjakortti">
    @if (session('purchase_error'))
        <div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B; padding:12px 16px; border-radius:8px; font-size:14px; margin-bottom:20px;">
            {{ session('purchase_error') }}
        </div>
    @endif

    <div style="background:white; border-radius: var(--brand-radius, 16px); padding:24px 28px; box-shadow:0 1px 2px rgba(0,0,0,0.03), 0 12px 32px -20px rgba(0,0,0,0.10); border:1px solid rgba(42,52,40,0.08); margin-bottom:24px;">
        <h1 style="font-family: var(--brand-heading-font); font-size: 24px; font-weight:700; color: var(--brand-text); margin:0 0 10px;">
            Osta lahjakortti
        </h1>
        <p style="font-family: var(--brand-body-font); font-size:15px; color: var(--brand-accent); opacity:0.75; margin:0;">
            Anna lahjaksi kurssi. Kortin voi käyttää maksuvälineenä ilmoittautuessa.
        </p>
    </div>

    @if (! $onlineEnabled)
        <div style="background:white; border-radius: var(--brand-radius, 16px); padding:28px;">
            <p style="color: var(--brand-text);">Lahjakortin ostaminen verkossa ei ole tällä hetkellä käytössä.</p>
        </div>
    @else
        <div style="background:white; border-radius: var(--brand-radius, 16px); padding:28px;">
            <form method="POST" action="{{ route('kurssit.public.gift-card.store') }}" style="display:flex; flex-direction:column; gap:14px; max-width:420px; margin:0 auto;">
                @csrf

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Summa</label>
                    <select name="amount" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                        <option value="20">20 €</option>
                        <option value="50">50 €</option>
                    </select>
                </div>

                <h3 style="font-family: var(--brand-heading-font); font-size:15px; font-weight:600; color: var(--brand-text); margin:8px 0 0;">Omat tietosi</h3>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Nimi</label>
                    <input type="text" name="purchaser_name" value="{{ old('purchaser_name') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                    @error('purchaser_name') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Sähköposti</label>
                    <input type="email" name="purchaser_email" value="{{ old('purchaser_email') }}" required style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                    @error('purchaser_email') <p style="color:#991B1B; font-size:13px; margin-top:4px;">{{ $message }}</p> @enderror
                </div>

                <h3 style="font-family: var(--brand-heading-font); font-size:15px; font-weight:600; color: var(--brand-text); margin:8px 0 0;">Kenelle annat lahjaksi? (valinnainen)</h3>
                <p style="font-size:13px; color: var(--brand-accent); opacity:0.75; margin:-8px 0 0;">Jos täytät nämä, kortti lähetetään suoraan hänelle. Muuten se lähetetään sinulle.</p>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Vastaanottajan nimi</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Vastaanottajan sähköposti</label>
                    <input type="email" name="recipient_email" value="{{ old('recipient_email') }}" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">
                </div>

                <div>
                    <label style="display:block; font-size:14px; font-weight:500; color: var(--brand-text); margin-bottom:4px;">Viesti (valinnainen)</label>
                    <textarea name="message" rows="3" style="width:100%; padding:10px 12px; border:1px solid #D1D5DB; border-radius: var(--brand-radius, 8px); box-sizing:border-box; font-family: var(--brand-body-font);">{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="btn-brand"
                    style="margin-top:8px; border:none; padding:12px 20px; border-radius: var(--brand-radius, 8px); font-size:15px; font-weight:600; cursor:pointer; font-family: var(--brand-body-font);">
                    Jatka maksuun
                </button>
            </form>
        </div>
    @endif
</x-kurssit::layouts.public>