#!/bin/bash
# Script de despliegue - Puntos Chanta
# Ejecutar desde la raíz del proyecto Laravel (donde está artisan)

set -e

echo "==> Entrando en modo mantenimiento..."
php artisan down --retry=60 || true

echo "==> Actualizando código desde Git..."
git pull origin Master

echo "==> Instalando dependencias PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Instalando dependencias Node y compilando assets..."
npm ci --no-audit --no-fund
npm run build

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Enlace de storage (si no existe)..."
php artisan storage:link 2>/dev/null || true

echo "==> Limpiando y cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Reiniciando colas (si se usan)..."
php artisan queue:restart 2>/dev/null || true

echo "==> Permisos de storage y cache..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "==> Saliendo del modo mantenimiento..."
php artisan up

echo "==> ¡Despliegue completado!"
