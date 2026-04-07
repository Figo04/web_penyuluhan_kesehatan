Copy

FROM php:8.4-cli
 
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip
 
# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
 
# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
 
WORKDIR /app
 
COPY . .
 
RUN composer install --optimize-autoloader --no-scripts --no-interaction --ignore-platform-reqs
 
RUN chmod -R 775 storage bootstrap/cache
 
EXPOSE 8080
 
# v2 - use php built-in server
CMD bash -c "php artisan migrate --force && php artisan config:cache && php artisan route:cache && php -S 0.0.0.0:${PORT:-8080} -t public"
 