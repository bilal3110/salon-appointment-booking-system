<!DOCTYPE html>
<html lang="en">
    <head>
        @include('frontend.layout.head')
    </head>
    <body class="@yield('body-class')" data-booking-url="{{ route('frontend.booking') }}">
        <a class="skip-link" href="#main">Skip to content</a>

        @include('frontend.layout.nav')

        <main id="main">
            @yield('main-section')
        </main>

        @include('frontend.layout.footer')
        @include('frontend.partials.glyphs')

        <script src="{{ asset('Frontend/js/script.js') }}" defer></script>
        @yield('scripts')
    </body>
</html>
