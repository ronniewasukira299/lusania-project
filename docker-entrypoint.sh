#!/bin/sh
set -e

# 1. Generate key if not set
php artisan key:generate --no-interaction --force

# 2. Create storage link
php artisan storage:link --force

# 3. Run migrations (now with env vars loaded)
php artisan migrate --force --no-interaction

# 4. Start Nginx in background
service nginx start

# 5. Start PHP-FPM in foreground (keeps container alive)
exec php-fpm