#!/bin/bash

# Output immediately to ensure logs are captured
echo "=== Starting Laravel Application ===" >&2
echo "PORT: $PORT" >&2
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')" >&2
echo "APP_ENV: ${APP_ENV:-not set}" >&2
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}" >&2
echo "DB_HOST: ${DB_HOST:-not set}" >&2
echo "DB_PORT: ${DB_PORT:-not set}" >&2

# Check if PORT is set
if [ -z "$PORT" ]; then
    echo "ERROR: PORT environment variable is not set!" >&2
    exit 1
fi

# Ensure storage directories exist and are writable
echo "Checking storage directories..." >&2
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache 2>&1
chmod -R 775 storage bootstrap/cache 2>&1 || echo "Warning: chmod failed (may not be critical)" >&2

# Clear config cache first (this doesn't require database)
echo "Clearing config cache..." >&2
php artisan config:clear 2>&1 || echo "Config clear failed (non-critical)" >&2

# Only clear other caches if database is configured (to avoid SQLite errors)
if [ -n "$DB_CONNECTION" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Clearing application caches..." >&2
    php artisan cache:clear 2>&1 || echo "Cache clear failed (non-critical)" >&2
fi

# Clear route and view caches (these don't require database)
php artisan route:clear 2>&1 || echo "Route clear failed (non-critical)" >&2
php artisan view:clear 2>&1 || echo "View clear failed (non-critical)" >&2

# Run migrations if database is configured
if [ -n "$DB_CONNECTION" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Running database migrations..." >&2
    
    # Check if migrations table exists by trying migrate:status
    # If it fails or returns empty, the database is likely empty
    MIGRATION_STATUS=$(php artisan migrate:status --force 2>&1)
    MIGRATION_EXIT_CODE=$?
    
    # If migrate:status fails or shows no migrations, run fresh
    if [ $MIGRATION_EXIT_CODE -ne 0 ] || echo "$MIGRATION_STATUS" | grep -qE "No migrations found|Nothing to migrate|migrations table doesn't exist|Base table or view not found" || [ -z "$MIGRATION_STATUS" ]; then
        echo "Database appears empty or migrations table missing. Running fresh migrations with seeding..." >&2
        php artisan migrate:fresh --force --seed 2>&1 || {
            echo "ERROR: Failed to run fresh migrations!" >&2
            exit 1
        }
        echo "Fresh migrations and seeding completed successfully" >&2
    else
        echo "Running pending migrations..." >&2
        php artisan migrate --force 2>&1 || {
            echo "WARNING: Migration failed, attempting fresh migration..." >&2
            php artisan migrate:fresh --force --seed 2>&1 || {
                echo "ERROR: Failed to run fresh migrations!" >&2
                exit 1
            }
            echo "Fresh migrations completed successfully" >&2
        }
        echo "Migrations completed successfully" >&2
    fi
fi

# If APP_KEY is not set, try to generate one (only if .env exists)
if [ -z "$APP_KEY" ]; then
    if [ -f ".env" ]; then
        echo "WARNING: APP_KEY not set, generating new key..." >&2
        php artisan key:generate --force 2>&1 || {
            echo "ERROR: Failed to generate APP_KEY!" >&2
            exit 1
        }
        echo "APP_KEY generated successfully" >&2
    else
        echo "ERROR: APP_KEY is not set and .env file does not exist!" >&2
        echo "Please set APP_KEY as an environment variable in Railway." >&2
        echo "You can generate one locally with: php artisan key:generate --show" >&2
        exit 1
    fi
fi

# Start the server
echo "Starting PHP server on 0.0.0.0:$PORT..." >&2
php artisan serve --host=0.0.0.0 --port=$PORT 2>&1

