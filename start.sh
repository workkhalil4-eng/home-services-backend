#!/bin/sh
# Ensure DB exists
mkdir -p database
touch database/database.sqlite

# Run migrations
php artisan migrate --force
php artisan db:seed --force

# Start the Laravel development server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
