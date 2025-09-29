# 🎟️ Laravel Event Booking System

A full-featured **Event Booking API** built with **Laravel 11** using **Sanctum authentication**, **role-based access control**, and **service-repository pattern**. This API supports events, tickets, bookings, payments, notifications, queues, caching, and testing.

---

## 🚀 Features

### 🔐 Authentication & Authorization
- **Registration, Login, Logout** using **Laravel Sanctum**
- **Role-based access control**:
  - **Admin** → Manage all events, tickets, bookings
  - **Organizer** → Manage their own events & tickets
  - **Customer** → Book tickets & view bookings
- Middleware protection for routes

### 📅 Event APIs
- `GET /api/events` → List events with pagination, search, filter by date/location
- `GET /api/events/{id}` → Event details with tickets
- `POST /api/events` → Create event (**organizer only**)
- `PUT /api/events/{id}` → Update event (**organizer only**)
- `DELETE /api/events/{id}` → Delete event (**organizer only**)

### 🎫 Ticket APIs
- `POST /api/events/{event_id}/tickets` → Create ticket (**organizer only**)
- `PUT /api/tickets/{id}` → Update ticket (**organizer only**)
- `DELETE /api/tickets/{id}` → Delete ticket (**organizer only**)

### 📝 Booking APIs
- `POST /api/tickets/{id}/bookings` → Create booking (**customer only**)
- `GET /api/bookings` → List user bookings
- `PUT /api/bookings/{id}/cancel` → Cancel booking

### 💳 Payment APIs
- `POST /api/bookings/{id}/payment` → Process payment (mock)
- `GET /api/payments/{id}` → View payment details
- PaymentService simulates success/failure

### 🔔 Notifications & Queues
- Notify customers when booking is confirmed
- Queue system for sending notifications
- Event list caching (`CACHE_DRIVER=file`)

### ⚙️ Extra
- Middleware to **prevent double booking** for the same ticket
- Trait `CommonQueryScopes` for reusable query scopes
- Clean **service-repository-controller architecture**
- **Factories & seeders** for Users, Events, Tickets, Bookings, Payments

---


## 🛠️ Installation & Setup

### 1. Clone the repository
```bash
git clone https://github.com/M-noor-88/laravel-event-booking.git
cd laravel-event-booking
```

### 2. Install dependencies
```
composer install
```
### 4. Configure environment
```
Copy .env.example to .env:

cp .env.example .env

```

### Update the database settings for MySQL:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_booking
DB_USERNAME=root
DB_PASSWORD=secret

CACHE_DRIVER=file

```
### Optional: configure mail driver for Gmail:
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="Event Booking App"
```

### 4. Generate application key
```
php artisan key:generate
```
### 5. Run migrations and seeders
```
php artisan migrate
php artisan db:seed
```

### Run the Server and Queue 

```
php artisan serve
```
### For Queue ⛏️ Sending Notification via Email 

![5967387257545544525](https://github.com/user-attachments/assets/a0ea50b8-6dea-4eaf-8c8d-ac22d242f4ce)

```
php artisan queue:work
```
### This will create all tables and seed:

#### Users

#### Events

#### Tickets

#### Bookings

#### Payments

### 🧪 Testing

Use SQLite for unit tests

Update .env.testing:
```
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```
#### Run tests
```
php artisan migrate
php artisan test
```



### All feature and unit tests are included:

#### Registration & Login

#### Event creation & update

#### Ticket booking

#### PaymentService unit tests

