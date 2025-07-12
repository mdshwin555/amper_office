FROM php:8.2-fpm

# تثبيت الحزم الأساسية
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    zip \
    nginx \
    supervisor

# تثبيت إضافات PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد مجلد العمل
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . .

# نسخ ملف البيئة إذا مو مضاف مسبقاً (تحقق بنفسك)
# COPY .env.example .env

# تثبيت حزم Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# توليد مفتاح التطبيق (لو ناقص)
RUN php artisan key:generate || true

# إعداد الصلاحيات للكتابة
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www

# نسخ إعدادات Nginx
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# نسخ إعدادات Supervisor
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# فتح البورت
EXPOSE 80

# تشغيل Supervisor
CMD ["/usr/bin/supervisord", "-n"]
