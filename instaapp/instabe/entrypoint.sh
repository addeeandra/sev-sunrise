#!/bin/bash
set -Eeo pipefail
set -o errexit    # Used to exit upon error, avoiding cascading errors

IFS=$'\n\t'

cd /var/www

echo "run on '$DOCKER_ENV' environment!"
echo "run with '$DOCKER_WORKERS' workers!"

php artisan optimize

# run with `octane` if available, otherwise fallback to `artisan serve`
if php artisan | grep -q "octane"; then
    if [ $DOCKER_ENV == "production" ]; then
        php artisan octane:start --host=0.0.0.0 --port=80 --workers=$DOCKER_WORKERS --task-workers=$DOCKER_WORKERS
    else
        php artisan octane:start --host=0.0.0.0 --watch --port=80 --workers=$DOCKER_WORKERS --task-workers=$DOCKER_WORKERS
    fi
else
    if [ $DOCKER_ENV == "production" ]; then
        echo "artisan serve is not running in APP_ENV=production"
        exit 1
    else
        php artisan serve --host=0.0.0.0 --port=80
    fi
fi