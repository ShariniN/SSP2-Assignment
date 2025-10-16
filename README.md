# 🛍️ Laravel E-Commerce Platform

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
