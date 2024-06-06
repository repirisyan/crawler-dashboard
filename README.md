# Crawler App Dashboard

Aplikasi ini merupakan halaman Admin dari series aplikasi crawler

## System Requirement
- php 8.2 or above
- nodeJs lts or bunjs
- mysql 8
- redis

## Instalation
1. composer install
2. npm run build
3. php artisan key:generate
4. php artisan migrate
5. php artisan db:seed
6. config .env
	- APP_CRAWLER_API='url_crawler_gateway'