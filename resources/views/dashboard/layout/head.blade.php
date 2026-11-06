<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Management Office') — The Garrison</title>

    <link rel="shortcut icon" href="{{ asset('Frontend/assets/icon.png') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Cormorant+Garamond:wght@400;500;600&family=Inter:wght@300;400;500;600&display=swap"
    />
    <link rel="stylesheet" href="{{ asset('dashboard/css/admin.css') }}">

    {{-- Marks the document as script-enabled so progressive enhancement stays predictable --}}
    <script>
        document.documentElement.classList.add('js');
    </script>
</head>

<body class="adm" data-rail="expanded" data-drawer="closed">
    @include('dashboard.layout.icons')
