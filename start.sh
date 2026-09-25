#!/bin/sh

# If DATABASE_URL is set, parse it for PostgreSQL
if [ -n "$DATABASE_URL" ]; then
    export DB_CONNECTION=pgsql
    export DB_HOST=$(echo $DATABASE_URL | sed -e 's|.*@\(.*\):.*|\1|')
    export DB_PORT=$(echo $DATABASE_URL | sed -e 's|.*:\([0-9]*\)/.*|\1|')
    export DB_DATABASE=$(echo $DATABASE_URL | sed -e 's|.*/\(.*\)$|\1|')
    export DB_USERNAME=$(echo $DATABASE_URL | sed -e 's|.*://\(.*\):.*@.*|\1|')
    export DB_PASSWORD=$(echo $DATABASE_URL | sed -e 's|.*://[^:]*:\(.*\)@.*|\1|')
    echo "Using PostgreSQL: $DB_HOST/$DB_DATABASE"
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
