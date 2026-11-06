@php
    $home = route('frontend.index');
    $menu = [
        ['label' => 'Home', 'href' => $home, 'note' => 'The House'],
        ['label' => 'Services', 'href' => $home . '#services', 'note' => 'Price List'],
        ['label' => 'About', 'href' => $home . '#about', 'note' => 'The House'],
        ['label' => 'Gallery', 'href' => $home . '#gallery', 'note' => 'Plates'],
        ['label' => 'Contact', 'href' => $home . '#contact', 'note' => 'Location'],
        ['label' => 'Rate Us', 'href' => route('frontend.testimonial'), 'note' => 'Credits'],
    ];
@endphp

<header class="nav" id="siteNav">
    <div class="shell nav__inner">
        <a class="wordmark" href="{{ $home }}" aria-label="The Garrison — return to the home page">
            <svg class="wordmark__crest" viewBox="0 0 48 56" aria-hidden="true" focusable="false">
                <path
                    d="M24 2 L43 8.6 V29.4 C43 40 33.6 47.6 24 52 C14.4 47.6 5 40 5 29.4 V8.6 Z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.4"
                />
                <path
                    d="M24 7.4 L38.4 12.6 V29.4 C38.4 37.8 31 44 24 47.6 C17 44 9.6 37.8 9.6 29.4 V12.6 Z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="0.7"
                    opacity="0.55"
                />
                <text
                    x="24"
                    y="33"
                    text-anchor="middle"
                    font-family="Cinzel, Georgia, serif"
                    font-size="15"
                    letter-spacing="0.5"
                    fill="currentColor"
                >
                    TG
                </text>
            </svg>
            <span class="wordmark__type">
                <span class="wordmark__name">The Garrison</span>
                <span class="wordmark__sub">Private Grooming Society</span>
            </span>
        </a>

        <nav class="nav__links" aria-label="Primary">
            <a class="nav__link" href="{{ $home }}">Home</a>
            <a class="nav__link" href="{{ $home }}#services">Services</a>
            <a class="nav__link" href="{{ $home }}#about">About</a>
            <a class="nav__link" href="{{ $home }}#gallery">Gallery</a>
            <a class="nav__link" href="{{ $home }}#contact">Contact</a>
            <a class="nav__link" href="{{ route('frontend.testimonial') }}">Rate Us</a>
        </nav>

        <div class="nav__actions">
            <a class="btn btn--gold nav__cta" href="{{ route('frontend.booking') }}">
                Book an Appointment
            </a>

            <button
                class="nav__toggle"
                id="menuToggle"
                type="button"
                aria-label="Open menu"
                aria-expanded="false"
                aria-controls="mobileNav"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<div class="drawer" id="mobileNav" aria-hidden="true">
    <div class="drawer__scrim" data-drawer-close></div>

    <div class="drawer__panel" role="dialog" aria-modal="true" aria-label="Site menu">
        <div class="drawer__head">
            <span class="wordmark__type">
                <span class="wordmark__name">The Garrison</span>
                <span class="wordmark__sub">Est. 1919 · Lyallpur</span>
            </span>
            <button class="drawer__close" type="button" data-drawer-close aria-label="Close menu">
                &times;
            </button>
        </div>

        <nav class="drawer__links" aria-label="Mobile">
            @foreach ($menu as $item)
                <a href="{{ $item['href'] }}">
                    {{ $item['label'] }}
                    <span>{{ $item['note'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="drawer__foot">
            <p class="drawer__meta">
                <strong>The Address</strong>
                Gulberg, Lyallpur — Faisalabad, Punjab
            </p>
            <p class="drawer__meta">
                <strong>Hours</strong>
                Mon — Fri 9:00 to 20:00 · Sat 9:00 to 18:00 · Sun Closed
            </p>
            <a class="btn btn--gold btn--block" href="{{ route('frontend.booking') }}">
                Book an Appointment
            </a>
        </div>
    </div>
</div>
