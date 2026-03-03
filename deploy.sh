#!/bin/bash
# Script de despliegue - Puntos Chanta
# Ejecutar desde la raíz del proyecto Laravel (donde está artisan)

set -e

echo "==> Entrando en modo mantenimiento..."
php artisan down --retry=60 || true

echo "==> Actualizando código desde Git..."
git fetch origin Master
git reset --hard origin/Master

echo "==> Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Instalando dependencias Node y compilando assets..."
# NVM en tmp (servidor Cloudways: home no escribible)
export NVM_DIR="${NVM_DIR:-$HOME/tmp/nvm}"
export npm_config_cache="${npm_config_cache:-$HOME/tmp/.npm}"
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
# Document root es public_html/public → los assets deben servirse en /build/assets/
export VITE_BASE_PATH=/build
npm ci --no-audit --no-fund --legacy-peer-deps
npm run build

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Enlace de storage (si no existe)..."
php artisan storage:link 2>/dev/null || true

echo "==> Limpiando todas las cachés de Laravel..."
php artisan optimize:clear

echo "==> Cacheando configuración para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "==> Limpiando OPcache de PHP..."
php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache reseteado.'; } else { echo 'OPcache no disponible via CLI.'; }" || true

echo "==> Reiniciando colas (si se usan)..."
php artisan queue:restart 2>/dev/null || true

echo "==> Permisos de storage y cache..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "==> Saliendo del modo mantenimiento..."
php artisan up

echo "==> ¡Despliegue completado!"
