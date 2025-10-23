# 🛍️ Laravel E-Commerce Platform

[![Laravel](https://img.shields.io/badge/Laravel-12-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Railway](https://img.shields.io/badge/Deployed%20on-Railway-blueviolet.svg)](https://railway.app)

A modern full-stack **Laravel** application featuring secure authentication, hybrid database architecture, and real-time interactivity — built for scalability and deployed on **Railway** using **Docker** and **GitHub Actions CI/CD**.

---

## 🚀 Features

### 🔐 Authentication & Security
- **JWT Authentication** for API access  
- **Laravel Sanctum & CSRF Protection** for SPA and secure session handling  
- **Laravel Jetstream** for user management, email verification, and 2FA  
- **Google OAuth (Socialite)** for social login  
- Secure token storage & refresh flow  

### 🧩 Architecture & Technologies
- **Backend:** Laravel 12 (PHP 8+)  
- **Frontend:** Blade + Livewire components  
  - **Search Bar** — dynamic product search  
  - **Cart System** — real-time cart updates without page reloads  
- **Databases:**  
  - **MySQL** — primary relational data store (users, orders, etc.)  
  - **MongoDB** — NoSQL store for wishlist 
- **Containerized** with **Docker** (multi-stage build)  

### ⚙️ DevOps & Deployment
- **CI/CD pipeline** powered by **GitHub Actions**  
  - Automated testing, linting, and deployment  
- **Hosted on Railway** with zero-downtime builds  
- Environment-based configuration using `.env`  

### 🧑‍💼 Admin Dashboard
- **Admin Backend** for product and inventory management  
- CRUD operations for categories, products, and users  
- Role-based access control (Admin / User)  

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-------------|
| Framework | Laravel 12 |
| Authentication | Jetstream, JWT, Sanctum, CSRF |
| Databases | MySQL, MongoDB |
| Frontend | Blade, Livewire |
| Social Login | Laravel Socialite (Google OAuth) |
| Deployment | Docker, Railway |
| CI/CD | GitHub Actions |
| Version Control | Git + GitHub |

---

## 🔧 Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Docker & Docker Compose
- MySQL 8.0+
- MongoDB 5.0+

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/ShariniN/ssp2.git
   cd ssp2
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your `.env` file**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_DATABASE=laravel_ecommerce
   
   MONGODB_CONNECTION=mongodb
   MONGODB_HOST=127.0.0.1
   MONGODB_DATABASE=laravel_wishlist
   
   GOOGLE_CLIENT_ID=your_google_client_id
   GOOGLE_CLIENT_SECRET=your_google_client_secret
   ```

5. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

   Access the application at `http://localhost:8000`

### Docker Setup

```bash
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

Access the application at `http://localhost:8000`

---

## 🚢 Deployment

### Automated Deployment (Current)
This project uses **GitHub Actions** for CI/CD:
- Push to `main` triggers automated tests
- On success, builds Docker image
- Deploys to Railway automatically

### Manual Deployment to Railway

1. Install Railway CLI: `npm i -g @railway/cli`
2. Login: `railway login`
3. Link project: `railway link`
4. Deploy: `railway up`

---

## 🐛 Troubleshooting

**MongoDB Connection Issues:**
```bash
# Check MongoDB is running
docker ps | grep mongo

# Verify connection in .env
MONGODB_CONNECTION=mongodb
```

**JWT Token Issues:**
```bash
php artisan jwt:secret
php artisan config:clear
```

**Livewire Not Updating:**
```bash
php artisan livewire:discover
npm run dev
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request



