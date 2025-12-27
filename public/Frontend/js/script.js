const $ = (id) => document.getElementById(id);

window.addEventListener("scroll", () => {
    const header = document.querySelector("header");
    window.scrollY > 50
        ? header.classList.add("scrolled")
        : header.classList.remove("scrolled");
});

const menuToggle = document.getElementById("menuToggle");
const mobileNav = document.getElementById("mobileNav");
const overlay = document.getElementById("overlay");
const navLinks = document.querySelectorAll(".nav-link");

function toggleMenu() {
    menuToggle.classList.toggle("active");
    mobileNav.classList.toggle("active");
    overlay.classList.toggle("active");
    document.body.style.overflow = mobileNav.classList.contains("active")
        ? "hidden"
        : "";
}

menuToggle.addEventListener("click", toggleMenu);
overlay.addEventListener("click", toggleMenu);

navLinks.forEach((link) => {
    link.addEventListener("click", () => {
        toggleMenu();
    });
});

window.addEventListener("resize", () => {
    if (window.innerWidth > 768 && mobileNav.classList.contains("active")) {
        toggleMenu();
    }
});

//Service API Render
async function getServices() {
    try {
        const res = await fetch("/api/services");
        if (!res.ok) throw new Error("Network response was not ok");
        const services = await res.json();
        console.log(services);
        return services;
    } catch (error) {
        console.error("Error fetching services:", error);
        return [];
    }
}

function renderServices(services) {
    const servicesContainer = document.getElementById("servicesContainer");

    servicesContainer.innerHTML = "";

    services.forEach((service) => {
        const serviceCard = document.createElement("div");
        serviceCard.className = `service-card ${service.service_type}`;
        serviceCard.innerHTML = `
      <h3 class="service-title">${service.service_name}</h3>
      <p class="service-description">
        ${service.description}
      </p>
      <p class="service-price">PKR ${service.price}</p>
      <a href="booking.html" class="service-cta">Book Appointment</a>
    `;
        servicesContainer.appendChild(serviceCard);
    });

    initializeFilters();
}

function initializeFilters() {
    const filterBtns = document.querySelectorAll(".filter-btn");
    const serviceCards = document.querySelectorAll(".service-card");

    filterBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            filterBtns.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");

            const category = btn.dataset.category;

            serviceCards.forEach((card) => {
                if (category === "all") {
                    card.style.display = "block";
                } else {
                    card.style.display = card.classList.contains(category)
                        ? "block"
                        : "none";
                }
            });
        });
    });
}

document.addEventListener("DOMContentLoaded", () => {
    getServices().then((services) => {
        renderServices(services);
    });
});

// Testimonial API Render
async function fetchTestimonials() {
  try {
    const response = await fetch("/api/testimonials");
    if (!response.ok) throw new Error("Network response was not ok");

    const result = await response.json();
    return result.data;
  } catch (error) {
    console.error("Error fetching testimonials:", error);
    return [];
  }
}


function renderStarRating(rating = 0) {
  let stars = "";
  for (let i = 1; i <= 5; i++) {
    stars += i <= rating ? "★" : "☆";
  }
  return stars;
}

function testimonialCard(t) {
  const name = t.name || t.user_name || "Anonymous";
  const initial = t.initial || name.charAt(0).toUpperCase();
  const profession = t.user_profession || t.profession || "";
  const message = t.message || t.text || t.testimonial || "";
  const rating = t.rating || 5;

  return `
    <div class="testimonial-card">
      <div class="quote-icon">"</div>
      <p class="testimonial-text">${message}</p>

      <div class="testimonial-author">
        <div class="author-avatar">${initial}</div>

        <div class="author-info">
          <div class="author-name">${name}</div>
          <div class="author-title">${profession}</div>
        </div>

        <div class="stars">${renderStarRating(rating)}</div>
      </div>
    </div>
  `;
}

document.addEventListener("DOMContentLoaded", async () => {
  const testimonials = await fetchTestimonials();
  const track = document.getElementById("carouselTrack");

  track.innerHTML =
    testimonials.map(testimonialCard).join("") +
    testimonials.map(testimonialCard).join("");
});
