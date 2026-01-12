# Use official PHP 8.2 with Nginx & PHP-FPM (works with Laravel 12)
FROM richarvey/nginx-php-fpm:3.1.0

# Set Laravel root directory
ENV WEBROOT /var/www/html/public

# Copy your project files
COPY . /var/www/html

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run Laravel commands during build
RUN php artisan key:generate --force
RUN php artisan migrate --force --seed
RUN php artisan storage:link