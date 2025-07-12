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

# مجلد العمل الصحيح
WORKDIR /var/www

# نسخ المشروع كامل
COPY . /var/www

# تثبيت الحزم
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# توليد مفتاح التطبيق
RUN php artisan key:generate || true

# صلاحيات
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www

# نسخ إعدادات Nginx و Supervisor
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# فتح البورت
EXPOSE 80

# بدء التشغيل
CMD ["/usr/bin/supervisord", "-n"]
