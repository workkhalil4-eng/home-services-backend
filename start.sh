#!/bin/sh

# If DATABASE_URL is set, let Laravel handle it natively
if [ -n "$DATABASE_URL" ]; then
    export DB_CONNECTION=pgsql
    echo "Using PostgreSQL via DATABASE_URL"
else
    # Fallback to SQLite
    export DB_CONNECTION=sqlite
    mkdir -p database
    touch database/database.sqlite
    echo "Using SQLite (DATABASE_URL not set)"
fi

# Run migrations (safe: only applies new migrations, never drops tables)
php artisan migrate --force

# Seed only if needed (idempotent: uses firstOrCreate)
php artisan db:seed --force

# Start the Laravel server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
