@php
    $currentUser = Auth::guard()->user();
    $isAdmin = $currentUser && $currentUser->type === 'admin';
@endphp

<aside class="adm-sidebar" id="admSidebar" aria-label="Primary">

    <div class="sidebar-brand">
        <span class="sidebar-brand__mark" aria-hidden="true">TG</span>
        <span class="sidebar-brand__text">
            <p class="sidebar-brand__name">The Garrison</p>
            <p class="sidebar-brand__role">Management Office</p>
        </span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-group">
            <p class="nav-group__label">Operations</p>

            <a href="{{ route('dashboard.index') }}" data-label="Dashboard"
                class="nav-link {{ request()->routeIs('dashboard.index') ? 'is-active' : '' }}"
                @if (request()->routeIs('dashboard.index')) aria-current="page" @endif>
                <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-layout-dashboard"></use></svg>
                <span class="nav-link__text">Dashboard</span>
            </a>

            <a href="{{ route('dashboard.bookings') }}" data-label="Appointments"
                class="nav-link {{ request()->routeIs('dashboard.bookings') ? 'is-active' : '' }}"
                @if (request()->routeIs('dashboard.bookings')) aria-current="page" @endif>
                <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-calendar-days"></use></svg>
                <span class="nav-link__text">Appointments</span>
            </a>

            @if ($isAdmin)
                <a href="{{ route('dashboard.clients') }}" data-label="Clients"
                    class="nav-link {{ request()->routeIs('dashboard.clients') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.clients')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-users"></use></svg>
                    <span class="nav-link__text">Clients</span>
                </a>
            @endif
        </div>

        @if ($isAdmin)
            <div class="nav-group">
                <p class="nav-group__label">The House</p>

                <a href="{{ route('dashboard.services') }}" data-label="Services"
                    class="nav-link {{ request()->routeIs('dashboard.services*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.services*')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-scissors"></use></svg>
                    <span class="nav-link__text">Services</span>
                </a>

                <a href="{{ route('dashboard.staff') }}" data-label="Staff"
                    class="nav-link {{ request()->routeIs('dashboard.staff*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.staff*')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-contact"></use></svg>
                    <span class="nav-link__text">Staff</span>
                </a>

                <a href="{{ route('dashboard.users') }}" data-label="Accounts"
                    class="nav-link {{ request()->routeIs('dashboard.users*') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.users*')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-id-card"></use></svg>
                    <span class="nav-link__text">Accounts</span>
                </a>
            </div>

            <div class="nav-group">
                <p class="nav-group__label">Standing</p>

                <a href="{{ route('dashboard.testimonials') }}" data-label="Reviews"
                    class="nav-link {{ request()->routeIs('dashboard.testimonials') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.testimonials')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-message-square-quote"></use></svg>
                    <span class="nav-link__text">Reviews</span>
                </a>

                <a href="{{ route('dashboard.reports') }}" data-label="Reports"
                    class="nav-link {{ request()->routeIs('dashboard.reports') ? 'is-active' : '' }}"
                    @if (request()->routeIs('dashboard.reports')) aria-current="page" @endif>
                    <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-chart-column"></use></svg>
                    <span class="nav-link__text">Reports</span>
                </a>
            </div>
        @endif

    </nav>

    <div class="sidebar-foot">
        @if ($isAdmin)
            <a href="{{ route('dashboard.settings') }}" data-label="Settings"
                class="nav-link {{ request()->routeIs('dashboard.settings') ? 'is-active' : '' }}"
                @if (request()->routeIs('dashboard.settings')) aria-current="page" @endif>
                <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-settings"></use></svg>
                <span class="nav-link__text">Settings</span>
            </a>
        @endif

        <a href="#" id="logoutButton" data-label="Sign out" class="nav-link">
            <svg class="icon nav-link__icon" aria-hidden="true"><use href="#i-log-out"></use></svg>
            <span class="nav-link__text">Sign Out</span>
        </a>

        <button type="button" class="rail-toggle" id="railToggle" aria-label="Collapse sidebar">
            <svg class="icon rail-toggle__icon" aria-hidden="true"><use href="#i-panel-left-close"></use></svg>
            <span class="rail-toggle__text">Collapse</span>
        </button>
    </div>

</aside>
