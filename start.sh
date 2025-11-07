#!/bin/bash

echo "=== Starting Laravel Application ==="
echo "PORT: $PORT"
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')"

# Clear config cache to ensure fresh environment variables are loaded
echo "Clearing config cache..."
php artisan config:clear || echo "Config clear failed (non-critical)"

# If APP_KEY is not set, generate one
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set, generating new key..."
    php artisan key:generate --force || echo "Key generation failed"
fi

# Start the server
echo "Starting PHP server on 0.0.0.0:$PORT..."
exec php artisan serve --host=0.0.0.0 --port=$PORT

