#!/bin/sh
set -e

# Instalar dependencias de desarrollo si no están instaladas
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

# Publicar assets de bundles (EasyAdmin, etc.) en public/bundles/
php bin/console assets:install public --symlink --relative 2>/dev/null || \
php bin/console assets:install public

exec php-fpm
