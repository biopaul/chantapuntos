# Despliegue - Puntos Chanta (chantas.ar)

## Importante: acceso SSH

Si al conectar por SSH ves **"Shell access is disabled!"**, el usuario no tiene terminal habilitada. Opciones:

1. **Habilitar SSH** para este usuario en el panel de Cloudways (Application → Access Details → SSH/SFTP).
2. **Usar el usuario principal** del servidor (ej. `master` o el que tenga SSH) en lugar de `deploy-chantas` para ejecutar los comandos de despliegue.
3. **Desplegar desde el panel** de Cloudways: muchas aplicaciones permiten "Deploy from Git" (clonar repo y ejecutar script de deploy desde la interfaz).

## Datos del servidor

- **Dominio:** https://chantas.ar/
- **IP:** 44.208.119.82
- **Usuario SSH:** deploy-chantas
- **Ruta aplicación:** `/public_html` (document root del sitio debe ser `public_html/public`)

## Primer despliegue (una sola vez)

Conectar por SSH y ejecutar en orden. Sustituir `$HOME` por la ruta real del usuario (ej. `/home/deploy-chantas`).

### 1. Ir al directorio donde esté public_html

```bash
cd $HOME/public_html
# O la ruta que indique tu panel (ej. /home/master/applications/chantas/public_html)
```

### 2. Clonar el repositorio (contenido en el directorio actual)

```bash
git clone https://github.com/biopaul/chantapuntos.git .
```

### 3. Crear y configurar .env

```bash
cp .env.example .env
```

Editar `.env` y dejar al menos estos valores de producción:

```env
APP_NAME="Puntos Chanta"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://chantas.ar

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=davmsrjqvv
DB_USERNAME=davmsrjqvv
DB_PASSWORD=JSuj6KhJtV

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Generar clave de aplicación:

```bash
php artisan key:generate
```

### 4. Instalar dependencias y compilar

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
```

### 5. Migraciones y cache

```bash
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Permisos

```bash
chmod -R 775 storage bootstrap/cache
# Si tienes usuario web (nginx/apache):
# chown -R www-data:www-data storage bootstrap/cache
```

### 7. Document root

El sitio debe apuntar a la carpeta **public** del proyecto. Es decir:

- Ruta del proyecto: `.../public_html/` (contiene app/, public/, etc.)
- Document root en el servidor: `.../public_html/public`

En Cloudways/panel, configurar el document root a `public_html/public` para el dominio chantas.ar.

---

## Despliegues posteriores

Desde la raíz del proyecto en el servidor:

```bash
cd $HOME/public_html   # o tu ruta
chmod +x deploy.sh
./deploy.sh
```

O ejecutar los comandos del script manualmente.

---

## Resolución de problemas

- **500 / permisos:** Revisar que `storage` y `bootstrap/cache` sean escribibles por el usuario del servidor web.
- **mix/manifest:** Ejecutar `npm run build` de nuevo.
- **Base de datos:** Comprobar en `.env` que DB_* coincidan con los datos del panel.
