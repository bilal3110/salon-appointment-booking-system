var el = document.getElementById("wrapper");
var toggleButton = document.getElementById("menu-toggle");

toggleButton.onclick = function () {
    el.classList.toggle("toggled");
};

// APIs
async function getbookings() {
    const response = await fetch("/api/bookings");
    const data = await response.json();
    return data;
}

async function renderBookings() {
    const bookings = await getbookings();
    const pendingBookings = bookings.filter(
        (booking) => booking.status === "pending"
    );
    const bookingsContainer = document.getElementById("bookings-container");

    bookingsContainer.innerHTML = "";

    pendingBookings.forEach((booking, index) => {
        const bookingCard = document.createElement("div");
        bookingCard.className = "booking-card";

        const bookingDate = new Date(booking.booking_date);
        const formattedDate = bookingDate.toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });

        bookingCard.innerHTML = `
            <div class="card-main">
                <div class="card-header">
                    <div class="booking-badge">Booking #${
                        booking.id || index + 1
                    }</div>
                    <div class="status-badge">${
                        booking.status || "Pending"
                    }</div>
                </div>

                <div class="card-body">
                    <div class="info-group">
                        <div class="icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Customer Name</div>
                            <div class="info-value">${
                                booking.customer_name || "N/A"
                            }</div>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">${
                                booking.customer_email || "N/A"
                            }</div>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Contact Number</div>
                            <div class="info-value">${
                                booking.customer_contact || "N/A"
                            }</div>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="icon">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Assigned Staff</div>
                            <div class="info-value">${
                                booking.staff?.user?.name || "N/A"
                            }</div>
                            <div class="info-sublabel">Staff ID: #${
                                booking.staff_id || "N/A"
                            }</div>
                        </div>
                    </div>

                    <div class="info-group">
                        <div class="icon">
                            <i class="fa-solid fa-scissors"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Service</div>
                            <div class="info-value">${
                                booking.service?.service_name || "N/A"
                            }</div>
                            <div class="info-sublabel">Service ID: #${
                                booking.service_id || "N/A"
                            }</div>
                        </div>
                    </div>

                    <div class="datetime-group">
                        <div class="datetime-item">
                            <div class="info-label">Date</div>
                            <div class="info-value">${formattedDate}</div>
                        </div>
                        <div class="datetime-item">
                            <div class="info-label">Time</div>
                            <div class="info-value">${
                                booking.booking_time || "N/A"
                            }</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-sidebar">
                <div class="price-section">
                    <div class="price-label">Total Amount</div>
                    <div class="price-value">PKR ${
                        booking.service?.price || 0
                    }</div>
                </div>

                <div class="card-actions">
                    <button class="btn btn-primary" onclick="confirmBooking(${
                        booking.id
                    })">Confirm Booking</button>
                    <button class="btn btn-secondary" onclick="cancelBooking(${
                        booking.id
                    })">Cancel Booking</button>
                </div>
            </div>
        `;

        bookingsContainer.appendChild(bookingCard);
    });
}

renderBookings();

async function confirmBooking(bookingId) {
    try {
        const response = await fetch(`/api/bookings/${bookingId}/confirm`, {
            method: "GET",
        });

        if (response.ok) {
            alert("Booking confirmed!");
            renderBookings();
        }
    } catch (error) {
        console.error("Error confirming booking:", error);
    }
}

async function cancelBooking(bookingId) {
    try {
        const response = await fetch(`/api/bookings/${bookingId}/cancel`, {
            method: "GET",
        });

        if (response.ok) {
            alert("Booking cancelled!");
            renderBookings();
        }
    } catch (error) {
        console.error("Error cancelling booking:", error);
    }
}

// Services APIs

async function getServices() {
    const response = await fetch("/api/services");
    const data = await response.json();
    return data;
}

async function renderServices() {
    const services = await getServices();
    const servicesContainer = document.getElementById("services-container");
    servicesContainer.innerHTML = "";
    services.forEach((service, index) => {
        const serviceCard = document.createElement("tr");
        let indexDisplay = index + 1;
        let serviceType = service.service_type ? service.service_type : "N/A";
        if (serviceType === "men") {
            serviceType = "Men";
        } else {
            serviceType = "Women";
        }

        serviceCard.innerHTML = `
            <th scope="row">${indexDisplay}</th>
            <td>${service.service_name}</td>
            <td>PKR ${service.price}</td>
            <td>${serviceType}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="loadEditServicePage(${service.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="DeleteService(${service.id})">Delete</button>
            </td>
        `;
        servicesContainer.appendChild(serviceCard);
    });
}
renderServices();

// Delete Service
async function DeleteService(serviceId) {
    try {
        const response = await fetch(`/api/services/${serviceId}`, {
            method: "DELETE",
        });
        if (response.ok) {
            alert("Service deleted successfully!");
            renderServices();
        }
    } catch (error) {
        console.error("Error deleting service:", error);
    }
}

// Create Service
const createServiceForm = document.getElementById("create-service-form");
if (createServiceForm) {
    createServiceForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const serviceName = document.getElementById("service-name").value;
        const servicePrice = document.getElementById("service-price").value;
        const serviceType = document.getElementById("service-type").value;
        const serviceDescription = document.getElementById(
            "service-description"
        ).value;

        const serviceData = {
            service_name: serviceName,
            price: servicePrice,
            service_type: serviceType,
            description: serviceDescription,
        };

        try {
            const response = await fetch("/api/services", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(serviceData),
            });

            if (response.ok) {
                alert("Service created successfully!");
                window.location.href = "/admin-panel/services";
            }
        } catch (error) {
            console.error("Error creating service:", error);
        }
    });
}

// Load edit service page
async function loadEditServicePage(serviceId) {
    window.location.href = "/admin-panel/services/" + serviceId + "/edit";
}

const editServiceForm = document.getElementById("edit-service-form");

if (editServiceForm) {
    const serviceId = editServiceForm.dataset.serviceId;

    editServiceForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const serviceName = document.getElementById("service-name").value;
        const servicePrice = document.getElementById("service-price").value;
        const serviceType = document.getElementById("service-type").value;
        const serviceDescription = document.getElementById(
            "service-description"
        ).value;

        const serviceData = {
            service_name: serviceName,
            price: servicePrice,
            service_type: serviceType,
            description: serviceDescription,
        };

        try {
            const response = await fetch(`/api/services/${serviceId}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(serviceData),
            });

            if (response.ok) {
                alert("Service updated successfully!");
                window.location.href = "/admin-panel/services";
            } else {
                const err = await response.json();
                alert("Update failed: " + (err.message || "Unknown error"));
            }
        } catch (error) {
            console.error("Error updating service:", error);
        }
    });
}

// Users Management can be added here

async function getUsers() {
    const response = await fetch("/api/users");
    const data = await response.json();
    return data;
}
async function renderUsers() {
    const users = await getUsers();
    const usersContainer = document.getElementById("users-container");
    usersContainer.innerHTML = "";
    users.forEach((user, index) => {
        const userRow = document.createElement("tr");
        let indexDisplay = index + 1;
        let userType = user.type ? user.type : "N/A";
        if (userType === "admin") {
            userType = "Admin";
        } else {
            userType = "Staff";
        }

        userRow.innerHTML = `
            <th scope="row">${indexDisplay}</th>
            <td>${user.name}</td>
            <td>${user.phone}</td>
            <td>${user.email}</td>
            <td>${userType}</td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editUser(${user.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Delete</button>
            </td>
        `;
        usersContainer.appendChild(userRow);
    });
}
renderUsers();

//Create User
const createUserForm = document.getElementById("create-user-form");
if (createUserForm) {
    createUserForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const userName = document.getElementById("user-name").value;
        const userEmail = document.getElementById("user-email").value;
        const userPhone = document.getElementById("user-phone").value;
        const userType = document.getElementById("user-type").value;
        const userPassword = document.getElementById("user-password").value;
        const userPasswordConfirm = document.getElementById(
            "password_confirmation"
        ).value;

        const userData = {
            name: userName,
            email: userEmail,
            phone: userPhone,
            type: userType,
            password: userPassword,
            password_confirmation: userPasswordConfirm,
        };

        try {
            const response = await fetch("/api/users", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(userData),
            });

            if (response.ok) {
                alert("User created successfully!");
                window.location.href = "/admin-panel/users";
            }
        } catch (error) {
            console.error("Error creating user:", error);
        }
    });
}
// Delete User
async function deleteUser(userId) {
    try {
        const response = await fetch(`/api/users/${userId}`, {
            method: "DELETE",
        });
        if (response.ok) {
            alert("User deleted successfully!");
            renderUsers();
        }
    } catch (error) {
        console.error("Error deleting user:", error);
    }
}

function editUser(userId) {
    window.location.href = "/admin-panel/users/" + userId + "/edit";
}

// Edit User
const editUserForm = document.getElementById("edit-user-form");

if (editUserForm) {
    const userId = editUserForm.dataset.userId;

    editUserForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const userName = document.getElementById("user-name").value;
        const userEmail = document.getElementById("user-email").value;
        const userPhone = document.getElementById("user-phone").value;
        const userType = document.getElementById("user-type").value;
        const passwordField = document.getElementById("user-password");
        const password_confirmationField = document.getElementById(
            "password_confirmation"
        );

        const userData = {
            name: userName,
            email: userEmail,
            phone: userPhone,
            type: userType,
        };

        if (passwordField && passwordField.value) {
            userData.password = passwordField.value;
            if (password_confirmationField) {
                userData.password_confirmation =
                    password_confirmationField.value;
            }
        }

        try {
            const response = await fetch(`/api/users/${userId}`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(userData),
            });

            if (response.ok) {
                alert("User updated successfully!");
                window.location.href = "/admin-panel/users";
            } else {
                const err = await response.json();
                alert("Update failed: " + (err.message || "Unknown error"));
            }
        } catch (error) {
            console.error("Error updating user:", error);
        }
    });
}

// Staff Management can be added here
async function getStaff() {
    const response = await fetch("/api/staff");
    const data = await response.json();
    return data;
}
async function renderStaff() {
    const staffMembers = await getStaff();
    const staffContainer = document.getElementById("staff-container");
    staffContainer.innerHTML = "";
    staffMembers.forEach((staff, index) => {
        const staffRow = document.createElement("tr");
        let indexDisplay = index + 1;

        staffRow.innerHTML = `
            <th scope="row">${indexDisplay}</th>
            <td>${staff.user?.name || "N/A"}</td>
            <td>${staff.user?.phone || "N/A"}</td>
            <td>${staff.user?.email || "N/A"}</td>
            <td><button class="btn btn-sm btn-info" onclick="window.location.href='/admin-panel/staff/' + ${
                staff.id
            } + '/view'">View</button></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editStaff(${
                    staff.id
                })">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteStaff(${
                    staff.id
                })">Delete</button>
            </td>
        `;
        staffContainer.appendChild(staffRow);
    });
}
renderStaff();

// View Staff
async function viewStaff(staffId) {
    const response = await fetch(`/api/staff/${staffId}`);
    const data = await response.json();
    return data;
}

// Render one staff details
async function renderStaffDetails() {
    const staffDetailsSection = document.getElementById("staff-details");
    const staffId = staffDetailsSection.dataset.staffId;
    const staff = await viewStaff(staffId);
    console.log(staff);

    const staffNameElement = staffDetailsSection.querySelector(".staff-name");
    if (staffNameElement) {
        staffNameElement.innerText = staff.user?.name ?? "N/A";
    }

    const staffBioElement = staffDetailsSection.querySelector(".staff-bio");
    if (staffBioElement) {
        staffBioElement.innerText = staff.bio || "N/A";
    }

    const staffPhotoElement = staffDetailsSection.querySelector(
        ".staff-photo-container img"
    );
    if (staffPhotoElement && staff.photo) {
        staffPhotoElement.src = staff.photo
            ? `/storage/${staff.photo}`
            : "staff_photos/default.png";
    }

    const staffInfoItems =
        staffDetailsSection.querySelectorAll(".staff-info-item");
    if (staffInfoItems.length > 0) {
        const emailSpan = staffInfoItems[0].querySelector(".staff-info-text");
        if (emailSpan) {
            emailSpan.innerText = staff.user?.email ?? "N/A";
        }
    }

    if (staffInfoItems.length > 1) {
        const phoneSpan = staffInfoItems[1].querySelector(".staff-info-text");
        if (phoneSpan) {
            phoneSpan.innerText = staff.user?.phone ?? "N/A";
        }
    }

    const servicesList = staffDetailsSection.querySelector(
        ".staff-services-list"
    );
    if (servicesList) {
        servicesList.innerHTML = "";

        if (staff.services && staff.services.length > 0) {
            staff.services.forEach((service) => {
                const serviceTag = document.createElement("div");
                serviceTag.className = "staff-service-tag";

                const serviceName = document.createTextNode(
                    service.service_name || "N/A"
                );
                serviceTag.appendChild(serviceName);

                if (service.service_type) {
                    const serviceType = document.createElement("span");
                    serviceType.className = "staff-service-type";
                    serviceType.innerText = service.service_type;
                    serviceTag.appendChild(serviceType);
                }

                servicesList.appendChild(serviceTag);
            });
        } else {
            const noServices = document.createElement("p");
            noServices.className = "staff-info-text";
            noServices.innerText = "No services assigned";
            servicesList.appendChild(noServices);
        }
    }
}

const staffDetailsSection = document.getElementById("staff-details");
if (staffDetailsSection) {
    renderStaffDetails();
}

const createStaffForm = document.getElementById("create-staff-form");

if (createStaffForm) {
    createStaffForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData();
        formData.append("user_id", document.getElementById("user-id").value);

        const serviceIds = Array.from(
            document.querySelectorAll('input[name="services[]"]:checked')
        ).map((cb) => cb.value);

        serviceIds.forEach((id) => formData.append("service_id[]", id));

        formData.append("bio", document.getElementById("bio").value);

        const photo = document.getElementById("photo").files[0];
        if (photo) formData.append("photo", photo);

        try {
            const res = await fetch("/api/staff", {
                method: "POST",
                body: formData,
            });

            const data = await res.json();
            console.log(data);

            if (res.ok) {
                alert("Staff created successfully!");
                window.location.href = "/admin-panel/staff";
            }
        } catch (error) {
            console.error("Error:", error);
        }
    });
}
