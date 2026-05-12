# ==========================================
# Stage 1: Build Node.js Assets (Vite)
# ==========================================
FROM node:18-alpine AS frontend-builder
WORKDIR /app
# نسخ ملفات الاعتماديات وتثبيتها
COPY package.json package-lock.json* ./
RUN npm install
# نسخ الكود المصدري وبناء أصول الواجهة (CSS/JS)
COPY . .
RUN npm run build

# ==========================================
# Stage 2: PHP-FPM & Nginx Environment
# ==========================================
FROM php:8.2-fpm-alpine

# تثبيت متطلبات النظام و Nginx
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    bash

# تثبيت إضافات PHP اللازمة لعمل Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مسار العمل
WORKDIR /var/www/html

# نسخ كامل مجلد المشروع
COPY . .

# نسخ مجلد البناء من المرحلة الأولى الخاص بالواجهة
COPY --from=frontend-builder /app/public/build ./public/build

# تثبيت حزم Composer للمشروع (وضع الإنتاج)
RUN composer install --no-dev --optimize-autoloader

# إعداد Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# إعداد سكربت البداية (Entrypoint)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# إعطاء الصلاحيات اللازمة למجلدات التخزين
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# فتح المنفذ 80 للويب
EXPOSE 80

# تشغيل السكربت عند بدء الحاوية
ENTRYPOINT ["entrypoint.sh"]
