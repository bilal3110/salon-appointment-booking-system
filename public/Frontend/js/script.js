const $ = (id) => document.getElementById(id);

// Header scroll effect
window.addEventListener("scroll", () => {
  const header = document.querySelector("header");
  window.scrollY > 50
    ? header.classList.add("scrolled")
    : header.classList.remove("scrolled");
});

// Burger Menu Toggle
const menuToggle = document.getElementById("menuToggle");
const mobileNav = document.getElementById("mobileNav");
const overlay = document.getElementById("overlay");
const navLinks = document.querySelectorAll(".nav-link");

function toggleMenu() {
  menuToggle.classList.toggle("active");
  mobileNav.classList.toggle("active");
  overlay.classList.toggle("active");
  document.body.style.overflow = mobileNav.classList.contains("active") ? "hidden" : "";
}

menuToggle.addEventListener("click", toggleMenu);
overlay.addEventListener("click", toggleMenu);

// Close menu when clicking on nav links
navLinks.forEach((link) => {
  link.addEventListener("click", () => {
    toggleMenu();
  });
});

// Close menu on window resize if it's open and screen becomes larger
window.addEventListener("resize", () => {
  if (window.innerWidth > 768 && mobileNav.classList.contains("active")) {
    toggleMenu();
  }
});
const filterBtns = document.querySelectorAll(".filter-btn");
const serviceCards = document.querySelectorAll(".service-card");

filterBtns.forEach((btn) => {
  btn.addEventListener("click", () => {
    filterBtns.forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    const category = btn.dataset.category;

    serviceCards.forEach((card) => {
      card.style.display = card.classList.contains(category) ? "block" : "none";
    });
  });
});

const testimonials = [
  {
    text:
      "An oasis of refinement in the heart of the city. The attention to detail and personalized service is unparalleled.",
    author: "Victoria Ashford",
    title: "Art Curator",
    initial: "V",
  },
  {
    text:
      "The epitome of understated elegance. Excellence is the standard here.",
    author: "James Wellington",
    title: "Investment Banker",
    initial: "J",
  },
  {
    text:
      "Consistency and sophistication combined. They understand true luxury.",
    author: "Charlotte Pemberton",
    title: "Gallery Owner",
    initial: "C",
  },
  {
    text:
      "Exceptional craftsmanship paired with a timeless atmosphere.",
    author: "Alexander Reed",
    title: "Corporate Attorney",
    initial: "A",
  },
  {
    text:
      "Discretion and professionalism of the highest order.",
    author: "Eleanor Whitmore",
    title: "Philanthropist",
    initial: "E",
  },
  {
    text:
      "A world where quality and tradition reign supreme.",
    author: "Theodore Blackwood",
    title: "Architect",
    initial: "T",
  },
];

function testimonialCard(t) {
  return `
    <div class="testimonial-card">
      <div class="quote-icon">"</div>
      <p class="testimonial-text">${t.text}</p>

      <div class="testimonial-author">
        <div class="author-avatar">${t.initial}</div>
        <div class="author-info">
          <div class="author-name">${t.author}</div>
          <div class="author-title">${t.title}</div>
        </div>
        <div class="stars">★★★★★</div>
      </div>
    </div>
  `;
}

const track = $("carouselTrack");
track.innerHTML = testimonials.map(testimonialCard).join("") + testimonials.map(testimonialCard).join("");