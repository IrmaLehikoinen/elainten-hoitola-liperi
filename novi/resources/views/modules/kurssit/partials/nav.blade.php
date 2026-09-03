<nav class="sp-nav">
    <a href="{{ route('sydanpolku.index') }}" class="sp-nav-brand">
        <svg viewBox="0 0 24 27" fill="none" stroke="var(--brand-primary)" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
            <path d="M12 19 C4 13.5 1 9 1 5.5 C1 2.5 3.2 1 5.5 1 C7.8 1 10 2.3 12 5 C14 2.3 16.2 1 18.5 1 C20.8 1 23 2.5 23 5.5 C23 9 20 13.5 12 19 Z" />
        </svg>
        Sydänpolku
    </a>

    <style>
        .sp-nav {
            display: flex;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 8px;
            border-bottom: 1px solid rgba(63,79,58,0.14);
        }
        .sp-nav-brand {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: var(--brand-heading-font);
            font-weight: 600;
            font-size: 18px;
            color: var(--brand-text);
            text-decoration: none;
        }
    </style>
</nav>