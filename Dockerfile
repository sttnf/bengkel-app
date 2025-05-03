FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install necessary packages including Nginx
RUN apk update && apk add --no-cache nginx php82-fpm php82-pdo_mysql php82-mbstring

# Copy your PHP application
COPY . /var/www/html

# Copy Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Expose port 80 for Nginx
EXPOSE 80

# Install and configure Supervisor
RUN apk add --no-cache supervisor
# Create the log directory for Supervisor
RUN mkdir -p /var/log/supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]