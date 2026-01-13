#!/bin/sh
set -e

# 1. Generate key if it's not already set in the environment
# This fixes the "Failed to open stream" error
php artisan key:generate --no-interaction --force

# 2. Create the storage link
php artisan storage:link --force

# 3. Run migrations (Wait for DB to be ready if necessary)
# Note: In production, you might want to run this manually instead
php artisan migrate --force --no-interaction

# 4. Start Nginx in the background
service nginx start

# 5. Start PHP-FPM in the foreground (this keeps the container alive)
exec php-fpm