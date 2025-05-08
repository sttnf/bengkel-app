#!/bin/bash

# Get the PORT environment variable with default to 80
PORT="${PORT:-80}"

# Create a new Nginx configuration with the correct port
cat > /etc/nginx/conf.d/default.conf << EOF
server {
    listen $PORT;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php\$is_args\$args;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Start PHP-FPM
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"