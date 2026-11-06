# The Garrison

**A grooming house with an appointment book, not a waiting room.**

The Garrison is a salon appointment-booking and operations app styled as a private grooming society in Lyallpur. Its public-facing experience takes cues from a heritage barbershop: editorial typography, warm walnut and oxblood tones, brass-like accents, craft-focused photography, and the language of a handwritten appointment ledger. Underneath the atmosphere is a practical workflow for choosing a service, matching it with an artisan, selecting a date and time, and managing the salon's day-to-day appointments.

## The problem

Salon appointments can end up scattered across phone calls, messages, paper diaries, and staff members' personal notes. That makes it harder for customers to understand the services and prices, choose the right person, and request a suitable time. It also leaves salon teams without one clear view of bookings, staff, and the service menu.

## How The Garrison helps

- **A clear place to start:** Customers can explore the salon, its services, prices, and testimonials before making an appointment.
- **A guided booking journey:** Customers select a treatment and an artisan, choose a date and time, and submit their contact details.
- **A connected service and staff list:** The booking experience can show which artisans provide a selected service.
- **One operational dashboard:** Authenticated salon users can review bookings and manage services, staff, and user accounts. Admins can view salon-wide bookings; staff users see their own bookings.
- **A consistent house identity:** The heritage-inspired presentation gives the booking experience and salon operations a recognizable, polished voice.

## What you can do

### For customers

- Browse the salon's service catalog and prices.
- Request an appointment by service, staff member, date, and time.
- Read and submit salon testimonials.

### For salon staff and administrators

- Review booking statuses and customer details.
- Add and edit services and prices.
- Manage staff profiles and the services each staff member offers.
- Manage user accounts and review client and testimonial information.
- See a dashboard overview of services, staff, and pending bookings.

## Technology

- **Backend:** PHP 8.1+ and Laravel 10
- **Database:** MySQL
- **Views:** Laravel Blade
- **Frontend:** HTML, CSS, and vanilla JavaScript
- **Assets:** Vite with the Laravel Vite plugin
- **Authentication and API support:** Laravel session authentication and Sanctum
- **Test and development tools:** PHPUnit, Faker, and Laravel Tinker

## Requirements

Install the following before setting up the project:

- PHP 8.1 or later, with the PHP extensions required by Laravel and the MySQL PDO driver
- [Composer](https://getcomposer.org/)
- [Node.js and npm](https://nodejs.org/)
- MySQL

## Install and run locally

### 1. Get the code and install dependencies

```bash
git clone https://github.com/bilal3110/salon-appointment-booking-system.git
cd salon-appointment-booking-system
composer install
npm install
```

### 2. Configure the environment

Create a local `.env` file from the example:

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

### 3. Create and configure a database

Create an empty MySQL database, then set its connection details in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=salon
DB_USERNAME=root
DB_PASSWORD=
```

Use the database name, user, and password configured for your own MySQL installation.

### 4. Create tables and load demo data

```bash
php artisan migrate --seed
php artisan storage:link
```

The demo seeder adds sample services, staff, bookings, testimonials, and an administrator account. It is safe to run again to fill in missing demo entries without clearing the database:

```bash
php artisan db:seed
```

Demo administrator:

- **Email:** `admin@salon.test`
- **Password:** `password`

This is a local testing account. Change or remove it before deploying the application.

### 5. Start the application

Run Laravel in one terminal:

```bash
php artisan serve
```

Run Vite in another terminal:

```bash
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Useful URLs

- **Salon website:** [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Appointment booking:** [http://127.0.0.1:8000/booking](http://127.0.0.1:8000/booking)
- **Testimonials:** [http://127.0.0.1:8000/testimonial](http://127.0.0.1:8000/testimonial)
- **Admin login:** [http://127.0.0.1:8000/admin-panel/login](http://127.0.0.1:8000/admin-panel/login)

## Development

Build production frontend assets with:

```bash
npm run build
```

Run the automated test suite with:

```bash
php artisan test
```

## License

This project is open-source software licensed under the [MIT License](LICENSE).
