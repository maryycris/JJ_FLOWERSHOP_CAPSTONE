#!/bin/bash
set -e

# Clear config cache to ensure fresh environment variables are loaded
php artisan config:clear || true

# Debug: Check if APP_KEY is set (remove in production if needed)
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')"

# If APP_KEY is not set, generate one (fallback - should not happen in production)
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set, generating new key..."
    php artisan key:generate --force || true
fi

# Start the server
php artisan serve --host=0.0.0.0 --port=$PORT

