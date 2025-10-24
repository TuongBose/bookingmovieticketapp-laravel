#!/bin/bash
set -e

echo "=== Đợi database khởi động... ==="
sleep 10  # hoặc dùng healthcheck sau này nếu muốn chính xác hơn

echo "=== Chạy migrate... ==="
php artisan migrate --force || true

echo "=== Init data example ==="
php artisan db:seed --force

echo "=== Khởi động Laravel Server ==="
php artisan serve --host=0.0.0.0 --port=8080
