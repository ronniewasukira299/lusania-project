# Use PHP 8.4 version of the image
FROM richarvey/nginx-php-fpm:3-php8.4

# Set Laravel public folder as web root
ENV WEBROOT /var/www/html/public

# Copy entire project
COPY . /var/www/html

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Run Laravel setup commands
RUN php artisan key:generate --force --no-interaction \
    && php artisan migrate --force --no-interaction \
    && php artisan storage:link

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache