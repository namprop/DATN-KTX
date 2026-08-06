# DATN-KTX Backend

REST API Laravel 12 cho hệ thống quản lý ký túc xá. API dùng Laravel Sanctum với Bearer token và middleware vai trò.

## Chạy local

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Backend mặc định chạy tại `http://localhost:8000`. Cấu hình database, mail, VNPay, frontend URL, CORS và OpenAI trong `.env`; không commit file này.

## Kiểm tra

```bash
php artisan config:clear
php artisan migrate:status
php artisan route:list
php artisan test
```

Chi tiết kiến trúc, ERD và luồng nghiệp vụ nằm trong [README dự án](../README.md).
