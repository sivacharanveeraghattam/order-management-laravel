# Order Management System REST API (Laravel)

Medium-Level PHP Developer Interview Task - Completed by Siva Charan

## Features
- Session-based Authentication (Register/Login)
- Product CRUD with full validation
- Order Creation (stock check, reduce stock, calculate total)
- Order Listing with user info & product details (No N+1 queries)
- Pagination, Soft Deletes, Transactions
- Seeders: 5 Users, 5 Products, 3 Sample Orders

## Setup Instructions (Copy-Paste Commands)
1. Clone repo
   git clone https://github.com/sivacharanveeraghattam/order-management-laravel.git
   cd order-management-laravel
   git checkout siva

2. Install
   composer install
   cp .env.example .env
   php artisan key:generate

3. Database (.env lo DB_DATABASE=order_db ani change chey)
   php artisan migrate
   php artisan session:table
   php artisan migrate
   php artisan db:seed   # Creates test data

4. Run
   php artisan serve
   API URL: http://127.0.0.1:8000/api

## Test Credentials
Email: test@example.com
Password: password123

## Postman Collection
Import file: postman/Order-Management-API.postman_collection.json
Flow: Login → Products CRUD → Create Order → List Orders

## Migration Files
All in database/migrations/ folder

Thank you! Ready for any questions.