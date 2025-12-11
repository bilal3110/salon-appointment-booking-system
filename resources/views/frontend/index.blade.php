@extends('frontend.layout.main')
@section('main-section')
  <body>
    @include('frontend.layout.nav')
    <div class="hero" id="home">
      <h1 class="hero-logo">THE GARRISON</h1>
      <p class="tagline">Private Grooming Society</p>
      <button
        class="cta-button"
        onclick="document.querySelector('.section').scrollIntoView({behavior: 'smooth'})"
      >
        Groomed Now
      </button>
    </div>

    <div class="section" id="services">
      <h2 class="section-title">Our Services</h2>

      <div class="filter-buttons">
          <button class="filter-btn active" data-category="all">All</button>
        <button class="filter-btn" data-category="men">Men</button>
        <button class="filter-btn" data-category="women">Women</button>
      </div>

      <div class="services-container" id="servicesContainer">
        <!-- Men Services -->
        {{-- <div class="service-card men">
          <h3 class="service-title">The Shelby Cut</h3>
          <p class="service-description">
            A precision cut befitting a gentleman of distinction. Sharp lines,
            impeccable fade, and the confidence of Birmingham royalty.
          </p>
          <p class="service-price">PKR 1,500</p>
          <a href="booking.html" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card men">
          <h3 class="service-title">The Royal Shave</h3>
          <p class="service-description">
            Hot towel treatment, straight razor shave, and bay rum aftershave.
            The way men were groomed in the golden era.
          </p>
          <p class="service-price">PKR 2,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card men">
          <h3 class="service-title">Beard Sculpting</h3>
          <p class="service-description">
            Meticulous trimming and shaping. Every gentleman deserves a beard
            that commands respect in any boardroom or betting shop.
          </p>
          <p class="service-price">PKR 18,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <!-- Women Services -->
        <div class="service-card women">
          <h3 class="service-title">The Grace Treatment</h3>
          <p class="service-description">
            Luxurious hair styling with vintage waves or sleek modern elegance.
            Because power dressing starts with perfect hair.
          </p>
          <p class="service-price">PKR 8,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card women">
          <h3 class="service-title">Polly's Polish</h3>
          <p class="service-description">
            Manicure and luxury hand treatment with deep burgundy or classic
            nude tones. Hands that command respect.
          </p>
          <p class="service-price">PKR 4,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card women">
          <h3 class="service-title">The Ada</h3>
          <p class="service-description">
            Full color service with balayage or rich jewel tones. Sophisticated,
            bold, and utterly unforgettable.
          </p>
          <p class="service-price">PKR 12,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card women">
          <h3 class="service-title">Hair & Scalp Revival</h3>
          <p class="service-description">
            Revitalizing treatment with premium oils and rose-infused steam.
            Because everyone deserves to unwind.
          </p>
          <p class="service-price">PKR 10,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card women">
          <h3 class="service-title">The Complete Affair</h3>
          <p class="service-description">
            Cut, style, and champagne on ice. Two hours of uninterrupted
            refinement in our private suite.
          </p>
          <p class="service-price">PKR 9,500</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div>

        <div class="service-card women">
          <h3 class="service-title">The Private Hour</h3>
          <p class="service-description">
            Exclusive suite service with complimentary beverage of choice. Your
            sanctuary, your rules. Discretion guaranteed.
          </p>
          <p class="service-price">PKR 23,000</p>
          <a href="" class="service-cta">Book Appointment</a>
        </div> --}}
      </div>
    </div>

    <div class="section about" id="about">
      <h2 class="section-title">By Order Of</h2>
      <p class="about-text">
        Since 1919, The Garrison has been a sanctuary for Lyallpur’s
        style-conscious souls. Our expert barbers and stylists craft every cut,
        color, and treatment with care that blends tradition with modern flair.
        <br /><br />
        Step inside where polished wood meets warm light, where chai meets
        conversation, and where every guest leaves feeling confident, refreshed,
        and unmistakably Lyallpur.
        <br /><br />
        No appointment? No worries. Good style favors patience, and our doors
        are always open for those who appreciate quality and tradition.
      </p>
    </div>

    <div class="testimonials-section">
      <div class="section-header">
        <p class="section-subtitle">What Our Clients Say</p>
        <h2 class="section-title">Testimonials</h2>
        <div class="section-divider"></div>
      </div>

      <div class="carousel-container">
        <div class="carousel-track" id="carouselTrack">
        </div>
      </div>
    </div>

  </body>
  <script src="{{asset('Frontend/js/script.js')}}"></script>
</html>
@endsection
