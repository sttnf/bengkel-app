FROM php:8.2-fpm-alpine

RUN apk add --no-cache --update libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql zip

WORKDIR /app

COPY . /app

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader


# Expose port (if your application serves directly - unlikely with FPM)
# EXPOSE 8000

# The default command for the PHP-FPM image is usually sufficient
# CMD ["php-fpm"]