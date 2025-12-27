<header>
    <div class="logo">
        <img src="{{ asset('Frontend/assets/logo.png') }}" alt="The Garrison Logo" />
    </div>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
    </button>
    <nav class="desktop-nav">
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#about">About Us</a></li>
            <li><a href="{{ route('frontend.testimonial') }}">Rate Us</a></li>
            <li><a href="{{ route('frontend.booking') }}">Book Now</a></li>
        </ul>
    </nav>
</header>
<nav class="mobile-nav" id="mobileNav">
    <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#about">About Us</a></li>
        <li><a href="{{ route('frontend.testimonial') }}">Rate Us</a></li>
        <li><a href="{{ route('frontend.booking') }}">Book Now</a></li>
    </ul>
</nav>
<div class="overlay" id="overlay"></div>
