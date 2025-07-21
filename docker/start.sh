#!/bin/sh

echo "Starting unified Laravel container..."

# Ensure proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Install/update composer dependencies if composer.json exists
if [ -f "/var/www/html/composer.json" ]; then
    echo "Installing/updating composer dependencies..."
    cd /var/www/html
    composer install --no-dev --optimize-autoloader 2>/dev/null || true
fi

# Run Laravel optimizations if artisan exists
if [ -f "/var/www/html/artisan" ]; then
    echo "Running Laravel optimizations..."
    cd /var/www/html
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

echo "Authentication is handled by Laravel middleware based on AUTH environment variable"
echo "Current AUTH setting: ${AUTH:-none}"

# Start supervisor which will manage nginx, php-fpm, ssh, workers, and cron
echo "Starting supervisor with all services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
