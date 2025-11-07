#!/bin/bash
set -e
php artisan config:clear || true
php artisan serve --host=0.0.0.0 --port=$PORT

