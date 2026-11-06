@php
    $currentUser = Auth::guard()->user();
    $isAdmin = $currentUser && $currentUser->type === 'admin';
    $initials = collect(preg_split('/\s+/', trim($currentUser->name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<header class="adm-topbar">
    <div class="topbar__lead">
        <button type="button" class="topbar__toggle" id="drawerToggle" aria-label="Open navigation"
            aria-expanded="false" aria-controls="admSidebar">
            <svg class="icon" aria-hidden="true"><use href="#i-menu"></use></svg>
        </button>

        <nav class="breadcrumb" aria-label="Breadcrumb">
            <span class="breadcrumb__item">@yield('section-name', 'The Garrison')</span>
            <span class="breadcrumb__sep" aria-hidden="true">
                <svg class="icon icon--xs"><use href="#i-chevron-right"></use></svg>
            </span>
            <span class="breadcrumb__item breadcrumb__item--current">@yield('page-name', 'Dashboard')</span>
        </nav>
    </div>

    <div class="topbar__spacer"></div>

    <div class="topbar__tools">

        <span class="topbar__date" title="{{ now()->format('l, j F Y') }}">
            <svg class="icon" aria-hidden="true"><use href="#i-calendar"></use></svg>
            {{ now()->format('D, j M') }}
        </span>

        @if ($isAdmin)
            <div class="gsearch" id="gsearch">
                <button type="button" class="gsearch__trigger" id="gsearchTrigger" aria-haspopup="true"
                    aria-expanded="false" aria-controls="gsearchPanel">
                    <svg class="icon" aria-hidden="true"><use href="#i-search"></use></svg>
                    <span>Search the house</span>
                    <kbd class="gsearch__kbd">/</kbd>
                </button>

                <div class="gsearch__panel" id="gsearchPanel" role="dialog" aria-label="Global search">
                    <input type="search" class="gsearch__input" id="gsearchInput" autocomplete="off"
                        placeholder="Search clients, appointments, services…" aria-label="Search the house">
                    <div class="gsearch__results" id="gsearchResults"></div>
                </div>
            </div>
        @endif

        <div class="profile" id="profileMenu">
            <button type="button" class="profile__trigger" id="profileTrigger" aria-haspopup="true"
                aria-expanded="false" aria-controls="profileMenuPanel">
                <span class="avatar" aria-hidden="true">{{ $initials }}</span>
                <span>
                    <span class="profile__name">{{ $currentUser->name }}</span>
                    <span class="profile__role">{{ $isAdmin ? 'Proprietor' : 'Barber' }}</span>
                </span>
                <svg class="icon icon--sm" aria-hidden="true"><use href="#i-chevron-down"></use></svg>
            </button>

            <div class="profile__menu" id="profileMenuPanel" role="menu">
                <div class="profile__head">
                    <span class="avatar" aria-hidden="true">{{ $initials }}</span>
                    <span>
                        <p class="profile__head-name">{{ $currentUser->name }}</p>
                        <p class="profile__head-mail">{{ $currentUser->email }}</p>
                    </span>
                </div>

                @if ($isAdmin)
                    <a class="profile__menu-item" href="{{ route('dashboard.settings') }}" role="menuitem">
                        <svg class="icon" aria-hidden="true"><use href="#i-settings"></use></svg>
                        Settings
                    </a>
                @endif

                <a class="profile__menu-item" href="{{ route('dashboard.bookings') }}" role="menuitem">
                    <svg class="icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                    Appointments
                </a>

                <a class="profile__menu-item" href="{{ route('frontend.index') }}" role="menuitem" target="_blank"
                    rel="noopener">
                    <svg class="icon" aria-hidden="true"><use href="#i-external-link"></use></svg>
                    View public site
                </a>

                <button type="button" class="profile__menu-item profile__menu-item--danger" id="logoutButtonProfile"
                    role="menuitem">
                    <svg class="icon" aria-hidden="true"><use href="#i-log-out"></use></svg>
                    Sign out
                </button>
            </div>
        </div>

    </div>
</header>
