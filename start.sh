#!/bin/bash

# Output immediately to ensure logs are captured
echo "=== Starting Laravel Application ===" >&2
echo "PORT: $PORT" >&2
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')" >&2
echo "APP_ENV: ${APP_ENV:-not set}" >&2
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}" >&2

# Check if PORT is set
if [ -z "$PORT" ]; then
    echo "ERROR: PORT environment variable is not set!" >&2
    exit 1
fi

# Ensure storage directories exist and are writable
echo "Checking storage directories..." >&2
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache 2>&1
chmod -R 775 storage bootstrap/cache 2>&1 || echo "Warning: chmod failed (may not be critical)" >&2

# Clear all caches to ensure fresh environment variables are loaded
echo "Clearing all caches..." >&2
php artisan config:clear 2>&1 || echo "Config clear failed (non-critical)" >&2
php artisan cache:clear 2>&1 || echo "Cache clear failed (non-critical)" >&2
php artisan route:clear 2>&1 || echo "Route clear failed (non-critical)" >&2
php artisan view:clear 2>&1 || echo "View clear failed (non-critical)" >&2

# If APP_KEY is not set, generate one
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set, generating new key..." >&2
    php artisan key:generate --force 2>&1 || {
        echo "ERROR: Failed to generate APP_KEY!" >&2
        exit 1
    }
    echo "APP_KEY generated successfully" >&2
fi

# Start the server
echo "Starting PHP server on 0.0.0.0:$PORT..." >&2
php artisan serve --host=0.0.0.0 --port=$PORT 2>&1

