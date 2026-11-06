@include('dashboard.layout.head')

<div class="adm-shell">
    @include('dashboard.layout.side')

    <div class="adm-scrim" data-drawer-close aria-hidden="true"></div>

    <div class="adm-main">
        @include('dashboard.layout.nav')

        <main class="adm-content">
            @yield('main-section')
        </main>
    </div>
</div>

<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="false"></div>

@include('dashboard.layout.footer')
