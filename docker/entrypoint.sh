#!/bin/bash

# الانتقال لمسار المشروع
cd /var/www/html

echo "🔧 تحسين الكاش الخاص بـ Laravel (Config, Routes, Views)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "⚙️ تشغيل المِجرايشنز (Migrations)..."
# سيتم التنفيذ إجبارياً في بيئة الإنتاج --force
php artisan migrate --force

echo "🚀 تجهيز وبدء خوادم PHP-FPM و Nginx..."

# تشغيل PHP-FPM في الخلفية
php-fpm -D

# تشغيل Nginx في الواجهة لإبقاء الحاوية تعمل
nginx -g "daemon off;"
