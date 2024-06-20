# Laravel API for ModaMingle Web Application

Web application for a clothing store that provides an API for managing products, handling orders, managing user profiles.
It includes 7 main API endpoints for:

* Categories
* Items
* Basket
* Orders
* Likes
* Bans
* Auth

## Technology Stack

* Laravel v10.x
* JWT Auth
* PostgreSQL

## Getting Started

```
git clone https://github.com/BNormalGroup/ShopApi.git
```
```
cd ShopApi
```
```
composer install
```
```
cp .env.example .env
```
```
php artisan key:generate
```
```
php artisan jwt:secret
```
```
Configure your database and other settings in the .env file
```
```
php artisan migrate
```
```
php artisan serve
```
