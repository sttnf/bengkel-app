FROM dunglas/frankenphp

WORKDIR /app

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN install-php-extensions pdo_mysql gd intl zip opcache \
    && composer install --no-dev --optimize-autoloader

# Copy application files and set permissions
COPY . .
RUN chown -R www-data:www-data /app && chmod -R 755 /app

# Configure PHP settings
ENV PHP_MEMORY_LIMIT=256M \
    PHP_UPLOAD_MAX_FILESIZE=20M \
    PHP_POST_MAX_SIZE=20M

# Expose port and start the application
EXPOSE 8000
CMD ["php", "cmd.php", "serve", "--host=0.0.0.0", "--skip-migrate"]