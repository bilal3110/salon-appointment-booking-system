<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="theme-color" content="#111111" />
<meta
    name="description"
    content="The Garrison — a private grooming society in Gulberg, Lyallpur. Tailored cuts, beard sculpting and hot towel shaves, by appointment since 1919."
/>

<title>@yield('title', 'The Garrison — Private Grooming Society')</title>

<meta name="csrf-token" content="{{ csrf_token() }}" />

<link rel="icon" href="{{ asset('Frontend/assets/favicon.svg') }}" type="image/svg+xml" />
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Inter:wght@300;400;500;600&display=swap"
/>

<link rel="stylesheet" href="{{ asset('Frontend/css/style.css') }}" />

{{-- Marks the document as script-enabled so reveal animations never hide content when JS is off --}}
<script>
    document.documentElement.classList.add('js');
</script>
