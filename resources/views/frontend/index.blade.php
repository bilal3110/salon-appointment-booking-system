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
