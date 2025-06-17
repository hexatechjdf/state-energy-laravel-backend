FROM php:8.2-cli

WORKDIR /app

# Install system dependencies & Composer
RUN apt-get update && apt-get install -y unzip libzip-dev && docker-php-ext-install zip pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port 10000
EXPOSE 10000

# Start Laravel using PHP built-in server
CMD php -S 0.0.0.0:10000 -t public
