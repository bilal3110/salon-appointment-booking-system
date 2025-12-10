const state = {
  service: null,
  servicePrice: 0,
  staff: null,
  date: null,
  time: null,
  currentMonth: new Date().getMonth(),
  currentYear: new Date().getFullYear(),
};

const timeSlotOptions = [
  "09:00 AM",
  "10:00 AM",
  "11:00 AM",
  "12:00 PM",
  "01:00 PM",
  "02:00 PM",
  "03:00 PM",
  "04:00 PM",
  "05:00 PM",
  "06:00 PM",
  "07:00 PM",
];

function renderCalendar() {
  const grid = document.getElementById("calendarGrid");
  const monthDisplay = document.getElementById("currentMonth");

  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  monthDisplay.textContent = `${monthNames[state.currentMonth]} ${
    state.currentYear
  }`;

  grid.innerHTML = "";

  const dayLabels = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];
  dayLabels.forEach((day) => {
    const label = document.createElement("div");
    label.className = "calendar-day-label";
    label.textContent = day;
    grid.appendChild(label);
  });

  const firstDay = new Date(state.currentYear, state.currentMonth, 1).getDay();
  const daysInMonth = new Date(
    state.currentYear,
    state.currentMonth + 1,
    0
  ).getDate();
  const today = new Date();

  for (let i = 0; i < firstDay; i++) {
    const emptyDay = document.createElement("div");
    emptyDay.className = "calendar-day empty";
    grid.appendChild(emptyDay);
  }

  for (let day = 1; day <= daysInMonth; day++) {
    const dayElement = document.createElement("div");
    dayElement.className = "calendar-day";
    dayElement.textContent = day;

    const dateStr = `${state.currentYear}-${String(
      state.currentMonth + 1
    ).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

    if (
      today.getDate() === day &&
      today.getMonth() === state.currentMonth &&
      today.getFullYear() === state.currentYear
    ) {
      dayElement.classList.add("today");
    }

    if (state.date === dateStr) {
      dayElement.classList.add("selected");
    }

    dayElement.addEventListener("click", () => selectDate(dateStr, day));
    grid.appendChild(dayElement);
  }
}

function selectDate(dateStr, day) {
  state.date = dateStr;
  updateSummary();
  renderCalendar();
  renderTimeSlots();
}

function renderTimeSlots() {
  const container = document.getElementById("timeSlots");
  container.innerHTML = "";

  timeSlotOptions.forEach((time) => {
    const slot = document.createElement("div");
    slot.className = "time-slot";
    slot.textContent = time;

    if (state.time === time) {
      slot.classList.add("selected");
    }

    slot.addEventListener("click", () => selectTime(time));
    container.appendChild(slot);
  });
}

function selectTime(time) {
  state.time = time;
  updateSummary();
  renderTimeSlots();
}

function updateSummary() {
  const serviceSelect = document.getElementById("serviceSelect");
  const staffSelect = document.getElementById("staffSelect");

  document.getElementById("summaryService").textContent =
    state.service || "Not selected";
  document.getElementById("summaryService").className = state.service
    ? "summary-value selected"
    : "summary-value";

  document.getElementById("summaryStaff").textContent =
    state.staff || "Not selected";
  document.getElementById("summaryStaff").className = state.staff
    ? "summary-value selected"
    : "summary-value";

  if (state.date) {
    const date = new Date(state.date + "T00:00:00");
    document.getElementById("summaryDate").textContent =
      date.toLocaleDateString("en-US", {
        month: "long",
        day: "numeric",
        year: "numeric",
      });
    document.getElementById("summaryDate").className = "summary-value selected";
  } else {
    document.getElementById("summaryDate").textContent = "Not selected";
    document.getElementById("summaryDate").className = "summary-value";
  }

  document.getElementById("summaryTime").textContent =
    state.time || "Not selected";
  document.getElementById("summaryTime").className = state.time
    ? "summary-value selected"
    : "summary-value";

  document.getElementById("totalPrice").textContent = state.servicePrice
    ? `$${state.servicePrice}`
    : "$0";

  const confirmBtn = document.getElementById("confirmBtn");
  confirmBtn.disabled = !(
    state.service &&
    state.staff &&
    state.date &&
    state.time
  );
}

document.getElementById("serviceSelect").addEventListener("change", (e) => {
  const option = e.target.options[e.target.selectedIndex];
  state.service = option.textContent;
  state.servicePrice = parseInt(option.dataset.price) || 0;
  updateSummary();
});

document.getElementById("staffSelect").addEventListener("change", (e) => {
  state.staff = e.target.options[e.target.selectedIndex].textContent;
  updateSummary();
});

document.getElementById("prevMonth").addEventListener("click", () => {
  if (state.currentMonth === 0) {
    state.currentMonth = 11;
    state.currentYear--;
  } else {
    state.currentMonth--;
  }
  renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click", () => {
  if (state.currentMonth === 11) {
    state.currentMonth = 0;
    state.currentYear++;
  } else {
    state.currentMonth++;
  }
  renderCalendar();
});

document.getElementById("confirmBtn").addEventListener("click", () => {
  alert(
    `Booking confirmed!\n\nService: ${state.service}\nStaff: ${
      state.staff
    }\nDate: ${new Date(
      state.date + "T00:00:00"
    ).toLocaleDateString()}\nTime: ${state.time}\nTotal: $${
      state.servicePrice
    }\n\nYou will receive a confirmation email shortly.`
  );
});

renderCalendar();
renderTimeSlots();
updateSummary();