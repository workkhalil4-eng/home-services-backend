#!/bin/sh

# Parse DATABASE_URL into Laravel env vars if present
if [ -n "$DATABASE_URL" ]; then
    export DB_CONNECTION=pgsql
    export DB_HOST=$(echo $DATABASE_URL | sed -e 's|.*@\(.*\):.*|\1|')
    export DB_PORT=$(echo $DATABASE_URL | sed -e 's|.*:\([0-9]*\)/.*|\1|')
    export DB_DATABASE=$(echo $DATABASE_URL | sed -e 's|.*/\(.*\)$|\1|')
    export DB_USERNAME=$(echo $DATABASE_URL | sed -e 's|.*://\(.*\):.*@.*|\1|')
    export DB_PASSWORD=$(echo $DATABASE_URL | sed -e 's|.*://[^:]*:\(.*\)@.*|\1|')
fi

# Run migrations (safe: only applies new migrations, never drops tables)
php artisan migrate --force

# Seed only if needed (idempotent: uses firstOrCreate)
php artisan db:seed --force

# Start the Laravel server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
