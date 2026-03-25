# Salon Garison - Salon Management System

**Salon Garison** is a modern, comprehensive Salon Management System built using the Laravel framework. It is designed to streamline salon operations by providing an intuitive interface for clients to book appointments and an efficient dashboard for administrators to manage services, staff, and bookings.

---

## 🚀 Features

### **Frontend (Client Side)**
- **Online Booking:** A user-friendly booking system allowing clients to schedule appointments seamlessly.
- **Testimonials:** A section for clients to view and share their experiences.
- **Service Catalog:** Detailed overview of the services offered by the salon.
- **Responsive Design:** Optimized for a great experience across all devices.

### **Admin Dashboard**
- **Overview Statistics:** A quick glance at total services, staff members, and pending bookings.
- **Booking Management:** Track and manage client appointments efficiently.
- **Staff Management:** Full CRUD operations for managing salon staff and assigning them to services.
- **Service Management:** Add, edit, or remove salon services and pricing.
- **User Management:** Secure access control for administrators and staff.

---

## 🛠️ Technology Stack

- **Backend:** [Laravel 10](https://laravel.com/) (PHP ^8.1)
- **Frontend:** Blade Templating, Vanilla CSS, JavaScript
- **Database:** MySQL
- **Asset Management:** [Vite](https://vitejs.dev/)
- **Authentication:** Laravel Sanctum

---

## ⚙️ Local Setup Instructions

Follow these steps to set up the project on your local machine:

### **1. Prerequisites**
Ensure you have the following installed:
- **PHP** (>= 8.1)
- **Composer**
- **Node.js & NPM**
- **MySQL** (or any supported database)

### **2. Clone the Repository**
```bash
git clone https://github.com/bilal3110/salon-appointment-booking-system.git
cd Salon-Garison
```

### **3. Install PHP Dependencies**
```bash
composer install
```

### **4. Install Frontend Dependencies**
```bash
npm install
```

### **5. Environment Configuration**
Copy the example environment file and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

### **6. Database Setup**
1. Create a new database in your database manager (e.g., PHPMyAdmin or MySQL CLI).
2. Open the `.env` file and update the database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

### **7. Run Migrations**
Run the database migrations to create the necessary tables:
```bash
php artisan migrate
```

### **8. Storage Link**
Link the storage directory to the public directory to enable file uploads:
```bash
php artisan storage:link
```

### **9. Create an Admin User**
Since there is no default seeder, you can create your first admin user using Tinker:
```bash
php artisan tinker
```
Then run the following in the Tinker shell:
```php
\App\Models\User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'type' => 'admin',
]);
```

### **10. Launch the Application**
Start the Laravel development server:
```bash
php artisan serve
```
In a separate terminal, run the asset bundler:
```bash
npm run dev
```

### **10. Access the App**
- **Frontend:** [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Admin Panel:** [http://127.0.0.1:8000/admin-panel/login](http://127.0.0.1:8000/admin-panel/login)

---

## 🛡️ Security
If you discover any security-related issues, please use the issue tracker or contact the maintainers directly.

---

## 📄 License
This project is open-sourced software licensed under the [MIT license](LICENSE).
