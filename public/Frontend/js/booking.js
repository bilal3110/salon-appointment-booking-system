const state = {
  userName: null,
  userPhone: null,
  userEmail: null,
  serviceId: null,
  serviceName: null,
  servicePrice: 0,
  staffId: null,
  staffName: null,
  date: null,
  time: null,
  currentMonth: new Date().getMonth(),
  currentYear: new Date().getFullYear(),
};

const timeSlotOptions = [
  "09:00 AM","10:00 AM","11:00 AM","12:00 PM","01:00 PM",
  "02:00 PM","03:00 PM","04:00 PM","05:00 PM","06:00 PM","07:00 PM"
];

// ===== Fetch Services =====
async function fetchServices() {
  try {
    const res = await fetch('/api/services');
    const services = await res.json();
    const serviceSelect = document.getElementById("serviceSelect");

    services.forEach(service => {
      const option = document.createElement("option");
      option.value = service.id;
      option.textContent = service.service_name;
      option.dataset.price = service.price;
      serviceSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Error fetching services:", err);
  }
}

// ===== Fetch Staff by Service =====
async function fetchStaff(serviceId) {
  const staffSelect = document.getElementById("staffSelect");
  staffSelect.innerHTML = '<option value="">Choose a staff member</option>';
  if (!serviceId) return;

  try {
    const res = await fetch(`/api/staff-by-service/${serviceId}`);
    const staff = await res.json();
    staff.forEach(member => {
      const option = document.createElement("option");
      option.value = member.id;
      option.textContent = member.name;
      staffSelect.appendChild(option);
    });
  } catch (err) {
    console.error("Error fetching staff:", err);
  }
}

// ===== Calendar =====
function renderCalendar() {
  const grid = document.getElementById("calendarGrid");
  const monthDisplay = document.getElementById("currentMonth");
  const monthNames = ["January","February","March","April","May","June","July",
    "August","September","October","November","December"];
  monthDisplay.textContent = `${monthNames[state.currentMonth]} ${state.currentYear}`;
  grid.innerHTML = "";

  const dayLabels = ["Su","Mo","Tu","We","Th","Fr","Sa"];
  dayLabels.forEach(day => {
    const label = document.createElement("div");
    label.className = "calendar-day-label";
    label.textContent = day;
    grid.appendChild(label);
  });

  const firstDay = new Date(state.currentYear, state.currentMonth, 1).getDay();
  const daysInMonth = new Date(state.currentYear, state.currentMonth+1,0).getDate();
  const today = new Date();

  for(let i=0;i<firstDay;i++){
    const empty = document.createElement("div");
    empty.className = "calendar-day empty";
    grid.appendChild(empty);
  }

  for(let day=1;day<=daysInMonth;day++){
    const dayEl = document.createElement("div");
    dayEl.className = "calendar-day";
    dayEl.textContent = day;

    const dateStr = `${state.currentYear}-${String(state.currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;

    if(today.getDate()===day && today.getMonth()===state.currentMonth && today.getFullYear()===state.currentYear){
      dayEl.classList.add("today");
    }
    if(state.date === dateStr){
      dayEl.classList.add("selected");
    }

    dayEl.addEventListener("click",()=>selectDate(dateStr));
    grid.appendChild(dayEl);
  }
}

function selectDate(dateStr){
  state.date = dateStr;
  renderCalendar();
  renderTimeSlots();
  updateSummary();
}

// ===== Time Slots =====
function renderTimeSlots(){
  const container = document.getElementById("timeSlots");
  container.innerHTML = "";
  timeSlotOptions.forEach(time=>{
    const slot = document.createElement("div");
    slot.className = "time-slot";
    slot.textContent = time;
    if(state.time === time) slot.classList.add("selected");
    slot.addEventListener("click",()=>selectTime(time));
    container.appendChild(slot);
  });
}

function selectTime(time){
  state.time = time;
  renderTimeSlots();
  updateSummary();
}

// ===== Summary Update =====
function updateSummary() {
  const summaryMap = {
    summaryName: state.userName || "Not provided",
    summaryPhone: state.userPhone || "Not provided",
    summaryEmail: state.userEmail || "Not provided",
    summaryService: state.serviceName || "Not selected",
    summaryStaff: state.staffName || "Not selected",
    summaryDate: state.date ? new Date(state.date).toLocaleDateString("en-US", { month:'long', day:'numeric', year:'numeric' }) : "Not selected",
    summaryTime: state.time || "Not selected",
    totalPrice: state.servicePrice ? `PKR ${state.servicePrice}` : "PKR 0"
  };

  Object.entries(summaryMap).forEach(([id, value]) => {
    const el = document.getElementById(id);
    if(el) el.textContent = value;
    if(id !== "totalPrice"){
      el.className = value.includes("Not") ? "summary-value" : "summary-value selected";
    }
  });

  document.getElementById("confirmBtn").disabled = !(
    state.serviceId && state.staffId && state.date && state.time &&
    state.userName && state.userPhone && state.userEmail
  );
}

// ===== Event Listeners =====
document.getElementById("serviceSelect").addEventListener("change",(e)=>{
  const option = e.target.selectedOptions[0];
  state.serviceId = option.value;
  state.serviceName = option.textContent;
  state.servicePrice = parseInt(option.dataset.price)||0;
  state.staffId = null;
  state.staffName = null;
  fetchStaff(state.serviceId);
  updateSummary();
});

document.getElementById("staffSelect").addEventListener("change",(e)=>{
  const option = e.target.selectedOptions[0];
  state.staffId = option.value;
  state.staffName = option.textContent;
  updateSummary();
});

["userName","userPhone","userEmail"].forEach(id => {
  document.getElementById(id).addEventListener("input",(e)=>{
    state[id] = e.target.value;
    updateSummary();
  });
});

document.getElementById("prevMonth").addEventListener("click",()=>{
  if(state.currentMonth===0){state.currentMonth=11; state.currentYear--;}
  else state.currentMonth--;
  renderCalendar();
});

document.getElementById("nextMonth").addEventListener("click",()=>{
  if(state.currentMonth===11){state.currentMonth=0; state.currentYear++;}
  else state.currentMonth++;
  renderCalendar();
});

// ===== Booking API =====
async function storeBooking(bookingData){
  try{
    const res = await fetch('/api/bookings',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body:JSON.stringify(bookingData)
    });
    if(!res.ok) throw new Error('Network error');
    return await res.json();
  }catch(err){
    console.error(err);
    throw err;
  }
}

document.getElementById("confirmBtn").addEventListener("click",async ()=>{
  const bookingData = {
    customer_name: state.userName,
    customer_contact: state.userPhone,
    customer_email: state.userEmail,
    service_id: state.serviceId,
    staff_id: state.staffId,
    booking_date: state.date,
    booking_time: state.time,
    notes: document.getElementById("userNotes").value,
    status:'Pending'
  };

  try{
    const result = await storeBooking(bookingData);
    alert(`Thank you, "${bookingData.customer_name}"! Your booking has been confirmed.`);
  }catch(err){
    alert('Failed to confirm booking. Please try again.');
  }
});

// ===== Initialize =====
fetchServices();
renderCalendar();
renderTimeSlots();
updateSummary();
