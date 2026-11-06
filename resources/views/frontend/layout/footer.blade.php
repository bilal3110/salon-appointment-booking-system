@php
    $home = route('frontend.index');
@endphp

<footer class="footer">
    <div class="shell">
        <div class="footer__grid">
            <div class="footer__brand">
                <span class="footer__wordmark">
                    <svg class="footer__crest" viewBox="0 0 48 56" aria-hidden="true" focusable="false">
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
                </span>

                <p class="footer__blurb">
                    An old-money grooming house in Gulberg, Lyallpur. Cut with a straight razor, kept with
                    quiet hands, and served by appointment only.
                </p>
            </div>

            <div>
                <h2 class="footer__heading">Explore</h2>
                <div class="footer__links">
                    <a href="{{ $home }}">Home</a>
                    <a href="{{ $home }}#services">Services</a>
                    <a href="{{ $home }}#about">About the House</a>
                    <a href="{{ $home }}#gallery">Gallery</a>
                    <a href="{{ $home }}#contact">Contact &amp; Location</a>
                    <a href="{{ route('frontend.testimonial') }}">Rate Us</a>
                </div>
            </div>

            <div>
                <h2 class="footer__heading">Visit</h2>
                <div class="footer__links">
                    <span>Gulberg, Lyallpur</span>
                    <span>Faisalabad, Punjab</span>
                    <span>Pakistan</span>
                    <a href="tel:03244050830">0324 4050830</a>
                    <a href="{{ route('frontend.booking') }}">Book an Appointment</a>
                </div>
            </div>

            <div>
                <h2 class="footer__heading">Hours</h2>
                <div class="footer__links">
                    <span>Monday — Friday · 9:00 — 20:00</span>
                    <span>Saturday · 9:00 — 18:00</span>
                    <span>Sunday · Closed</span>
                    <span>Walk-ins by discretion</span>
                </div>
            </div>
        </div>

        <div class="footer__bar">
            <span>&copy; {{ date('Y') }} The Garrison — Private Grooming Society. All rights reserved.</span>
            <em>By order of the gentleman.</em>
        </div>
    </div>
</footer>
