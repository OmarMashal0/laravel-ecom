# Laravel E-Commerce Platform

A feature-rich, modern e-commerce platform built with the latest Laravel ecosystem. This system provides a seamless shopping experience for customers and a powerful administrative dashboard for store management.

## 🚀 Live Demo
You can explore the live application here: [https://ecommerce.free.laravel.cloud/](https://ecommerce.free.laravel.cloud/)

---

## 🛠️ Tech Stack
This project leverages cutting-edge technologies for performance, scalability, and developer experience:
- **Framework**: [Laravel 12.x](https://laravel.com/)
- **Admin Panel**: [Filament v4.0](https://filamentphp.com/) (with Filament Shield for Role-Based Access Control)
- **Frontend**: [Livewire 3](https://livewire.laravel.com/) (using Volt and Flux components)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/)
- **Payments**: [Stripe](https://stripe.com/) & Cash on Delivery (COD)
- **Authentication**: Laravel Fortify
- **Database**: MySQL / PostgreSQL / SQLite

---

## ✨ Key Features

### 🛒 Storefront (Customer-Facing)
- **Advanced Product Browsing**: Filter products by category, brand, and price range.
- **Persistent Shopping Cart**: Manage items with real-time updates using Livewire.
- **Smart Search**: Quickly find products with an integrated search bar.
- **Secure Checkout**: Multi-step checkout process with shipping address management.
- **Customer Portal**: View order history, track status, and manage profile settings.
- **Review System**: Rate and review products to build community trust.
- **Discount System**: Apply coupon codes for instant discounts.

### 🛡️ Admin Dashboard (Management)
- **Product Management**: Full CRUD for products, including support for multiple variants and image galleries.
- **Brand & Category Organization**: Structured organization for a large inventory.
- **Order Management**: Complete lifecycle tracking—from pending to delivered.
- **Customer Insights**: Manage customer profiles and view order histories.
- **Marketing Tools**: Create and manage promotional coupons.
- **Analytics & Trends**: Visualized data for sales tracking and store performance.
- **Role-Based Access**: Granular permissions for administrative staff using Filament Shield.

---

## 💻 Local Setup

Follow these steps to get the project running on your local machine:

### 1. Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- A local database (MySQL, PostgreSQL, or SQLite)

### 2. Installation
Clone the repository:
```bash
git clone https://github.com/OmarMashal0/laravel-ecom.git
```

Install PHP dependencies:
```bash
composer install
```

Install and build frontend assets:
```bash
npm install
npm run build
```

### 3. Configuration
Create your environment file:
```bash
cp .env.example .env
```
Update the `.env` file with your database, mail, and Stripe credentials.

Generate the application key:
```bash
php artisan key:generate
```

### 4. Database Setup
Run the migrations:
```bash
php artisan migrate
```

### 5. Running the Application
Start the development server:
```bash
php artisan serve
```
The application will be available at `http://127.0.0.1:8000`.

---

## 💳 Payment Integration
The system is pre-configured for **Stripe**. To enable payments:
1. Obtain your API keys from the [Stripe Dashboard](https://dashboard.stripe.com/test/apikeys).
2. Add your `STRIPE_KEY` and `STRIPE_SECRET` to the `.env` file.
