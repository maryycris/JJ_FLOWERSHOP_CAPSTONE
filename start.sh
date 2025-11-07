#!/bin/bash

# Output immediately to ensure logs are captured
echo "=== Starting Laravel Application ===" >&2
echo "PORT: $PORT" >&2
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')" >&2

# Check if PORT is set
if [ -z "$PORT" ]; then
    echo "ERROR: PORT environment variable is not set!" >&2
    exit 1
fi

# Clear config cache to ensure fresh environment variables are loaded
echo "Clearing config cache..." >&2
php artisan config:clear 2>&1 || echo "Config clear failed (non-critical)" >&2

# If APP_KEY is not set, generate one
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set, generating new key..." >&2
    php artisan key:generate --force 2>&1 || echo "Key generation failed" >&2
fi

# Start the server
echo "Starting PHP server on 0.0.0.0:$PORT..." >&2
php artisan serve --host=0.0.0.0 --port=$PORT 2>&1

