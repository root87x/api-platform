#!/bin/sh

php bin/console lexik:jwt:generate-keypair --overwrite

if [[ "$APP_ENV" == "dev" ]]; then
    composer install --no-scripts --no-progress --no-interaction
fi

composer dump-autoload --optimize

php bin/console cache:warmup
php bin/console assets:install
php bin/console d:m:m

echo "rr initialized";
rr serve -c .rr.yaml
