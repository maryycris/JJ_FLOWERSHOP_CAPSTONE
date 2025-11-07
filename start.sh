#!/bin/bash

# Clear config cache to ensure fresh environment variables are loaded
php artisan config:clear 2>/dev/null || true

# Debug: Check if APP_KEY is set
if [ -n "$APP_KEY" ]; then
    echo "APP_KEY is set: YES"
else
    echo "APP_KEY is set: NO"
    echo "WARNING: APP_KEY not set, generating new key..."
    php artisan key:generate --force 2>/dev/null || true
fi

# Start the server
exec php artisan serve --host=0.0.0.0 --port=$PORT

