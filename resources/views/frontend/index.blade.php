@extends('frontend.layout.main')

@section('title', 'The Garrison — Private Grooming Society | Lyallpur')

@section('body-class', 'page-home')

@section('main-section')
    {{-- ============================ HERO ============================ --}}
    <section class="hero" id="home">
        <div class="hero__media">
            <img
                src="{{ asset('Frontend/assets/gallery/hero-salon.jpg') }}"
                alt="The Garrison floor after dark — oxblood leather barber chairs beneath warm tungsten lamps"
                fetchpriority="high"
                decoding="async"
                width="2000"
                height="1116"
            />
        </div>
        <div class="hero__scrim"></div>

        <div class="shell hero__inner">
            <div class="hero__content">
                <p class="eyebrow">Est. 1919 — Lyallpur</p>

                <h1 class="display display--xl hero__title">
                    A Gentleman's
                    <em>Standard.</em>
                </h1>

                <p class="lead hero__lead">
                    An old-money grooming house in Gulberg. Every cut is drawn by hand, every shave taken
                    with a warm blade, and every gentleman seated by appointment alone.
                </p>

                <div class="hero__actions">
                    <a class="btn btn--gold" href="{{ route('frontend.booking') }}">
                        Book Your Appointment
                        <span class="btn__arrow" aria-hidden="true">&rarr;</span>
                    </a>
                    <a class="btn btn--ghost" href="#services">Explore Our Services</a>
                </div>
            </div>
        </div>

        <div class="seal hero__stamp" aria-hidden="true">
            <span>Est.<br />1919<br />Gulberg</span>
        </div>

        <p class="hero__caption">
            <strong>The Garrison</strong>
            <span>Gulberg &middot; Lyallpur &middot; Faisalabad</span>
        </p>

        <div class="hero__scroll" aria-hidden="true">
            <span>Scroll</span>
            <span></span>
        </div>
    </section>

    {{-- ============================ BAND ============================ --}}
    <div class="band">
        <div class="shell band__inner">
            <div class="band__item">
                <span class="band__label">Established</span>
                <span class="band__value">1919, Gulberg</span>
            </div>
            <div class="band__item">
                <span class="band__label">Discipline</span>
                <span class="band__value">Gentlemen &amp; Ladies</span>
            </div>
            <div class="band__item">
                <span class="band__label">Attendance</span>
                <span class="band__value">By Appointment</span>
            </div>
        </div>
    </div>

    {{-- ============================ ABOUT ============================ --}}
    <section class="section" id="about">
        <div class="shell">
            <div class="about">
                <div class="about__media" data-reveal>
                    <div class="frame">
                        <img
                            class="frame__img"
                            src="{{ asset('Frontend/assets/gallery/master-barber.jpg') }}"
                            alt="A master barber honing a straight razor against the window light"
                            loading="lazy"
                            decoding="async"
                            width="1400"
                            height="1875"
                        />
                        <span class="frame__veil"></span>
                    </div>

                    <div class="about__badge">
                        <span class="about__badge-label">The House</span>
                        <span class="about__badge-value">Cutting since 1919</span>
                    </div>
                </div>

                <div class="about__content">
                    <div class="section-head" data-reveal>
                        <p class="eyebrow">The Establishment</p>
                        <h2 class="display display--md">
                            Hereditary Craft,<br />
                            <span class="serif-em">House Rules</span>
                        </h2>
                    </div>

                    <p class="body-text" data-reveal data-reveal-delay="1">
                        The Garrison was opened in 1919 on a Lyallpur side street that smelled of pomade,
                        coal smoke and strong tea. Three generations later the room is warmer and the
                        blades sharper, but the rules have not moved a yard: consult before you cut,
                        finish before you leave, and never rush a gentleman who is being shaved.
                    </p>

                    <p class="body-text" data-reveal data-reveal-delay="2">
                        The salon keeps two chairs for the house trade — tailoring, beard sculpting, hot
                        towel shaves and treatment — and a small book of appointments for the ladies'
                        floor. Same water. Same light. Same quiet hands.
                    </p>

                    <blockquote class="quote" data-reveal data-reveal-delay="3">
                        <p>
                            &ldquo;A gentleman leaves the chair better than he arrived. Not merely
                            groomed &mdash; composed.&rdquo;
                        </p>
                        <cite>House Rule, the first</cite>
                    </blockquote>

                    <div class="principles" data-reveal data-reveal-delay="4">
                        <div class="principle">
                            <span class="principle__num">01</span>
                            <div>
                                <h3 class="principle__title">A consultation before a cut</h3>
                                <p class="principle__text">
                                    We read the head, the hairline and the day before the first snip of the
                                    shears.
                                </p>
                            </div>
                        </div>
                        <div class="principle">
                            <span class="principle__num">02</span>
                            <div>
                                <h3 class="principle__title">Tools sharpened every morning</h3>
                                <p class="principle__text">
                                    Straight razors are honed on the leather strop and reset before the
                                    doors are unlocked.
                                </p>
                            </div>
                        </div>
                        <div class="principle">
                            <span class="principle__num">03</span>
                            <div>
                                <h3 class="principle__title">Time given, not sold</h3>
                                <p class="principle__text">
                                    Every service is booked with its full chair time, so nothing is rushed
                                    and nothing is double-booked.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ SERVICES ============================ --}}
    <section class="section section--walnut" id="services">
        <div class="shell">
            <div class="services">
                <aside class="services__aside">
                    <div class="section-head" data-reveal>
                        <p class="eyebrow">The Price List</p>
                        <h2 class="display display--md">
                            Services &amp;<br />
                            <span class="serif-em">Rates</span>
                        </h2>
                    </div>

                    <p class="body-text" data-reveal data-reveal-delay="1">
                        Every rate below includes consultation, the service itself, and a finish. Prices
                        are in Pakistani Rupees and are settled at the counter.
                    </p>

                    <div class="filter" role="group" aria-label="Filter services by house floor" data-reveal data-reveal-delay="2">
                        <button class="filter__btn is-active" type="button" data-filter="all" aria-pressed="true">
                            All
                            <span class="filter__count" data-count="all">&mdash;</span>
                        </button>
                        <button class="filter__btn" type="button" data-filter="men" aria-pressed="false">
                            Gentlemen
                            <span class="filter__count" data-count="men">&mdash;</span>
                        </button>
                        <button class="filter__btn" type="button" data-filter="women" aria-pressed="false">
                            Ladies
                            <span class="filter__count" data-count="women">&mdash;</span>
                        </button>
                    </div>
                </aside>

                <div>
                    <div class="ledger" id="servicesLedger" aria-live="polite" aria-busy="true">
                        <div class="state">
                            <span class="state__mark">&hellip;</span>
                            <span class="state__title">Consulting the ledger</span>
                            <span class="state__text">Fetching the house price list&hellip;</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ CLOSING CALL ============================ --}}
    <section class="closing">
        <div class="shell closing__inner">
            <div data-reveal>
                <p class="eyebrow">The Ledger Is Open</p>
                <h2 class="display display--lg closing__title">
                    Reserve Your <span class="serif-em">Chair</span>
                </h2>
                <p class="closing__text">
                    Name the treatment, choose the artisan, take the hour that suits you. A confirmation
                    call follows once the book is settled.
                </p>
            </div>

            <a class="btn btn--gold" href="{{ route('frontend.booking') }}" data-reveal data-reveal-delay="1">
                Book an Appointment
                <span class="btn__arrow" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </section>

    {{-- ============================ GALLERY ============================ --}}
    <section class="section section--deep" id="gallery">
        <div class="shell">
            <div class="section-head section-head--split" data-reveal>
                <div>
                    <p class="eyebrow">The Plates</p>
                    <h2 class="display display--md">
                        Inside the <span class="serif-em">House</span>
                    </h2>
                </div>
                <p class="body-text">
                    The floor, the tools and the light — photographed on an ordinary evening, when the
                    lamps are low and the chairs are still warm.
                </p>
            </div>

            <div class="gallery">
                @php
                    $plates = [
                        ['gallery/hero-salon.jpg', 'wide', 'The Floor', 'Gulberg, after hours', 2000, 1116],
                        ['gallery/master-barber.jpg', 'half', 'The Master', 'Straight razor, honed', 1400, 1875],
                        ['gallery/still-tools.jpg', 'even', 'The Tools', 'Steel, leather, badger', 1600, 1195],
                        ['gallery/chair-detail.jpg', 'even', 'The Chair', 'Oxblood leather, 1920s', 1600, 1195],
                        ['gallery/wall-tools.jpg', 'half', 'The Wall', 'Clippers and combs', 1400, 1875],
                        ['gallery/ledger-table.jpg', 'even', 'The Ledger', 'Whisky and a fountain pen', 1600, 1195],
                        ['gallery/hot-towel.jpg', 'half', 'The Shave', 'Hot towels, bay rum', 1400, 1875],
                        ['gallery/artisan-hands.jpg', 'half', 'The Finish', 'A steady hand, a warm towel', 1400, 1875],
                        ['gallery/floor-tiles.jpg', 'third', 'The Tiles', 'Hexagon tile, polished', 1024, 1024],
                        ['head.jpg', 'wide', 'The Ladies\' Floor', 'Silk, shadow, and a steady hand', 1600, 954],
                    ];
                @endphp

                @foreach ($plates as $index => $plate)
                    <figure
                        class="gallery__item gallery__item--{{ $plate[1] }}"
                        data-reveal
                        data-reveal-delay="{{ ($index % 3) + 1 }}"
                    >
                        <div class="gallery__ratio">
                            <img
                                src="{{ asset('Frontend/assets/' . $plate[0]) }}"
                                alt="{{ $plate[2] }} — {{ $plate[3] }}"
                                loading="lazy"
                                decoding="async"
                                width="{{ $plate[4] }}"
                                height="{{ $plate[5] }}"
                            />
                        </div>
                        <figcaption class="gallery__caption">
                            <span class="gallery__caption-title">{{ $plate[2] }}</span>
                            <span class="gallery__caption-tag">{{ $plate[3] }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================ TESTIMONIALS ============================ --}}
    <section class="section" id="testimonials">
        <div class="shell">
            <div class="section-head section-head--split" data-reveal>
                <div>
                    <p class="eyebrow">Letters of Credit</p>
                    <h2 class="display display--md">
                        What Our <span class="serif-em">Guests</span> Say
                    </h2>
                </div>
                <div>
                    <p class="body-text">
                        Every note below was left by a guest who sat in one of our chairs. We keep them
                        the way the old house kept its ledger — plainly, and in full.
                    </p>
                    <p style="margin-top: 18px">
                        <a class="link-line" href="{{ route('frontend.testimonial') }}">
                            Leave your own credit
                        </a>
                    </p>
                </div>
            </div>

            <div class="credits" id="creditsList" aria-live="polite" aria-busy="true">
                <div class="state">
                    <span class="state__mark">&hellip;</span>
                    <span class="state__title">Opening the ledger</span>
                    <span class="state__text">Fetching the guests&rsquo; notes&hellip;</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ CONTACT ============================ --}}
    <section class="section section--deep" id="contact">
        <div class="shell">
            <div class="section-head" data-reveal>
                <p class="eyebrow">Find Us</p>
                <h2 class="display display--md">
                    The Address &amp; <span class="serif-em">Hours</span>
                </h2>
            </div>

            <div class="contact">
                <div class="contact__details" data-reveal data-reveal-delay="1">
                    <div>
                        <h3 class="subhead">The House</h3>
                        <p class="body-text">
                            Gulberg, Lyallpur<br />
                            Faisalabad, Punjab<br />
                            Pakistan
                        </p>
                    </div>

                    <div>
                        <h3 class="subhead">The Telephone</h3>
                        <p class="body-text">
                            <a class="link-line" href="tel:03244050830">0324 4050830</a>
                        </p>
                    </div>

                    <div>
                        <h3 class="subhead">When We Are Open</h3>
                        <div class="hours">
                            <div class="hours__row">
                                <span class="hours__day">Monday — Friday</span>
                                <span class="hours__time">9:00 — 20:00</span>
                            </div>
                            <div class="hours__row">
                                <span class="hours__day">Saturday</span>
                                <span class="hours__time">9:00 — 18:00</span>
                            </div>
                            <div class="hours__row is-closed">
                                <span class="hours__day">Sunday</span>
                                <span class="hours__time">Closed</span>
                            </div>
                        </div>
                    </div>

                    <a class="btn btn--gold" href="{{ route('frontend.booking') }}">
                        Book an Appointment
                        <span class="btn__arrow" aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="contact__map" data-reveal data-reveal-delay="2">
                    <iframe
                        title="Map of Gulberg, Faisalabad showing the location of The Garrison"
                        src="https://maps.google.com/maps?q=Gulberg%2C%20Faisalabad%2C%20Punjab%2C%20Pakistan&t=&z=13&ie=UTF8&iwloc=&output=embed"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                    <div class="contact__map-bar">
                        <span>Gulberg &middot; Lyallpur &middot; Faisalabad</span>
                        <a
                            class="link-line"
                            href="https://www.google.com/maps/search/?api=1&query=Gulberg%2C+Faisalabad%2C+Punjab%2C+Pakistan"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            Open in Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
