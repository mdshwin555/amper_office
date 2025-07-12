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

# تثبيت الإضافات الخاصة بـ PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إنشاء مجلد العمل
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . .

# تثبيت الحزم باستخدام Composer (وحل مشكلة الامتدادات)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# إعطاء الصلاحيات
RUN chown -R www-data:www-data /var/www

# نسخ إعدادات nginx
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# نسخ إعدادات supervisor لتشغيل php-fpm و nginx سوا
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# فتح البورت
EXPOSE 80

# بدء Supervisor لتشغيل الخدمات
CMD ["/usr/bin/supervisord", "-n"]
