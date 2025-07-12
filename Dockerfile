FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . /var/www

# Install dependencies with composer
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www

# Copy nginx config
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port
EXPOSE 80

# Start supervisor to run both nginx and php-fpm
CMD ["/usr/bin/supervisord", "-n"]
