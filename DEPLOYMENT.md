# Exicompras — Guía de instalación y despliegue

> Marketplace multi-vendor Laravel 12 + Aimeos 2025.10
> Despliegue soportado: **con Docker** (recomendado) o **sin Docker** (clásico)
> Destinos: **Laravel Cloud**, **Coolify** (self-hosted), o cualquier VPS con Docker

---

## 📋 Tabla de contenidos

1. [Requisitos del sistema](#1-requisitos-del-sistema)
2. [Stack soportado](#2-stack-soportado)
3. [Elegir el modo de despliegue](#3-elegir-el-modo-de-despliegue)
4. [Opción A — Despliegue con Docker (recomendado)](#4-opción-a--despliegue-con-docker-recomendado)
   - [A.1 Local con Docker Compose](#a1-local-con-docker-compose)
   - [A.2 Despliegue en Laravel Cloud con Docker](#a2-despliegue-en-laravel-cloud-con-docker)
   - [A.3 Despliegue en Coolify con Docker](#a3-despliegue-en-coolify-con-docker)
5. [Opción B — Despliegue sin Docker (clásico)](#5-opción-b--despliegue-sin-docker-clásico)
   - [B.1 Local con Laragon / Valet / Sail](#b1-local-con-laragon--valet--sail)
   - [B.2 Despliegue en Laravel Cloud sin Docker](#b2-despliegue-en-laravel-cloud-sin-docker)
   - [B.3 Despliegue en Coolify sin Docker (Nixpacks)](#b3-despliegue-en-coolify-sin-docker-nixpacks)
   - [B.4 Despliegue en VPS clásico (Forge-style)](#b4-despliegue-en-vps-clásico-forge-style)
6. [Post-instalación](#6-post-instalación)
7. [Configuración de Aimeos](#7-configuración-de-aimeos)
8. [Tareas programadas y colas](#8-tareas-programadas-y-colas)
9. [HTTPS / SSL](#9-https--ssl)
10. [Backups](#10-backups)
11. [Troubleshooting](#11-troubleshooting)
12. [Notas de despliegue en Coolify (dockercompose)](#12-notas-de-despliegue-en-coolify-dockercompose)

---

## 1. Requisitos del sistema

### Requisitos de la aplicación

| Componente | Versión mínima | Notas |
|---|---|---|
| **PHP** | 8.2 (recomendado 8.3) | `intl` y `gd` obligatorios para Aimeos |
| **Composer** | 2.7+ | Solo si despliegas sin Docker |
| **Node.js** | 20+ (recomendado 22 LTS) | Para compilar assets con Vite |
| **Base de datos** | MySQL 8.0+ / MariaDB 10.11+ / PostgreSQL 15+ | SQLite solo para dev |
| **Redis** | 7+ | Recomendado para cache/sesiones/colas |
| **Extensiones PHP obligatorias** | `intl`, `mbstring`, `gd`, `pdo_mysql`, `pdo_sqlite`, `bcmath`, `curl`, `openssl`, `tokenizer`, `xml`, `zip`, `exif`, `fileinfo` | El Dockerfile las compila todas |
| **Extensiones opcionales** | `pcntl`, `opcache` (JIT), `redis` (PECL) | Para mejor rendimiento |

### Requisitos de hardware (producción)

| Recurso | Mínimo | Recomendado |
|---|---|---|
| **CPU** | 2 vCPU | 4+ vCPU (Aimeos es CPU-intensive) |
| **RAM** | 2 GB | 4 GB+ (con OPcache + JIT) |
| **Disco** | 20 GB | 50 GB SSD |
| **Red** | 100 Mbps | 1 Gbps |

---

## 2. Stack soportado

```
┌──────────────────────────────────────────────────────────────┐
│  Frontend:  Vite 8 + Tailwind 4 + Alpine.js 3                │
│  Backend:   Laravel 12.x + PHP 8.3                          │
│  E-commerce: Aimeos 2025.10 (multi-vendor)                  │
│  DB:        MySQL 8 / MariaDB 10.11+ / PostgreSQL 15+       │
│  Cache:     Redis 7+ (recomendado)                          │
│  Queue:     Redis 7+ / Database                             │
│  Storage:   Local / S3 (recomendado en producción)          │
└──────────────────────────────────────────────────────────────┘
```

---

## 3. Elegir el modo de despliegue

| Modo | Pros | Contras | Cuándo usarlo |
|---|---|---|---|
| **🐳 Docker (recomendado)** | Entorno reproducible, mismo stack en dev/prod, fácil de escalar, ideal para Coolify/Laravel Cloud | Requiere conocimientos de Docker, imágenes ocupan espacio | Equipos, producción, Coolify, Laravel Cloud |
| **📦 Sin Docker (clásico)** | Más simple para VPS únicos, integración nativa con Forge/Panel | Difícil de replicar, "funciona en mi máquina", versionado del sistema operativo | VPS único con Forge, Laragon en dev |

> **Recomendación:** Usa Docker en todos los entornos. Te ahorra horas de debugging y garantiza paridad total entre dev y producción.

---

## 4. Opción A — Despliegue con Docker (recomendado)

### A.1 Local con Docker Compose

#### Requisitos locales
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) o Docker Engine + Compose v2 (Linux)
- Al menos 4 GB de RAM libres
- Puertos libres: `80`, `3306`, `6379`, `8080`

#### Pasos

```powershell
# 1. Clona el repositorio
git clone https://github.com/tu-usuario/exicompras.git
cd exicompras

# 2. Copia el .env de Docker
cp .env.docker.example .env

# 3. (Opcional) Edita contraseñas y APP_URL
# APP_URL=http://localhost:8080
# DB_PASSWORD=tu-password-seguro

# 4. Levanta el stack
docker compose up -d --build

# 5. Espera a que el healthcheck pase (30-60s)
docker compose ps

# 6. Ejecuta las migraciones de Laravel + Aimeos
docker compose exec app php artisan migrate --force --seed

# 7. Crea el usuario admin inicial de Aimeos
docker compose exec app php artisan aimeos:account

# 8. (Opcional) Habilita el modo dev con Xdebug
cp docker-compose.override.yml.example docker-compose.override.yml
docker compose up -d
```

#### Verificar

Abre `http://localhost:8080` en el navegador. Verás la home de Exicompras.

```powershell
# Ver logs en vivo
docker compose logs -f app

# Ejecutar comandos dentro del container
docker compose exec app php artisan tinker
docker compose exec app composer require vendor/package
docker compose exec app npm run build
```

#### Comandos útiles

```powershell
# Detener todo
docker compose down

# Detener y eliminar volúmenes (⚠️ borra la BD)
docker compose down -v

# Ver uso de recursos
docker stats

# Acceder al container
docker compose exec app bash

# Reconstruir solo el servicio `app`
docker compose up -d --build app

# Escalar workers de cola
docker compose up -d --scale queue=3
```

---

### A.2 Despliegue en Laravel Cloud con Docker

[Laravel Cloud](https://cloud.laravel.com) detecta automáticamente el `Dockerfile` en la raíz del proyecto y lo usa para construir la imagen.

#### Requisitos previos
- Repositorio en GitHub/GitLab/Bitbucket
- Cuenta en [Laravel Cloud](https://cloud.laravel.com)
- Dominio personalizado (opcional pero recomendado)

#### Pasos

##### Paso 1 — Sube tu código

```powershell
git init
git add .
git commit -m "feat: dockerize exicompras"
git remote add origin https://github.com/tu-usuario/exicompras.git
git push -u origin main
```

##### Paso 2 — Crea la aplicación en Laravel Cloud

1. Entra a [cloud.laravel.com](https://cloud.laravel.com) → **New Application**
2. Conecta tu repositorio de GitHub
3. Laravel Cloud detectará automáticamente:
   - Que es un proyecto Laravel (por `composer.json`)
   - Que tiene un `Dockerfile` y lo usará para construir
4. Selecciona la región más cercana a tus usuarios

##### Paso 3 — Configura las variables de entorno

En el dashboard de Laravel Cloud → **Environment Variables**, añade:

```bash
APP_NAME=Exicompras
APP_ENV=production
APP_KEY=                              # Laravel Cloud lo puede generar automáticamente
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Database (Laravel Cloud aprovisiona MySQL automáticamente)
DB_CONNECTION=mysql
# Las variables DB_* las inyecta Laravel Cloud

# Cache y sesiones
CACHE_STORE=database                   # o redis si lo agregas
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=exicompras-uploads

# Mail
MAIL_MAILER=resend                     # o smtp
RESEND_API_KEY=...
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com
```

> 💡 Laravel Cloud genera automáticamente `APP_KEY` y `DB_*` si no los defines.

##### Paso 4 — Provisiona la base de datos

1. En el dashboard → **Database** → **Provision Database**
2. Laravel Cloud crea una instancia de MySQL gestionada y te da las credenciales
3. Copia los valores al `.env` (o Laravel Cloud los inyecta automáticamente)

##### Paso 5 — Despliega

1. Click en **Deploy**
2. Laravel Cloud:
   - Construye la imagen con tu `Dockerfile`
   - Despliega el container
   - Ejecuta el `entrypoint.sh` (que hace migrate, optimize, etc.)
3. Espera ~5-10 minutos

##### Paso 6 — Configura el dominio

1. En **Settings** → **Domains** → añade `tudominio.com`
2. Configura los DNS según las instrucciones de Laravel Cloud
3. SSL se configura automáticamente con Let's Encrypt

##### Paso 7 — Tareas programadas (opcional)

Laravel Cloud detecta automáticamente las tareas programadas en `routes/console.php` o `app/Console/Kernel.php`. No necesitas configurar cron manualmente.

#### Verificar

Abre tu dominio. La aplicación debería estar funcionando.

#### Actualizar

```powershell
git add .
git commit -m "feat: nueva funcionalidad"
git push
# Laravel Cloud detecta el push y redespliega automáticamente
```

---

### A.3 Despliegue en Coolify con Docker

[Coolify](https://coolify.io) es una alternativa self-hosted a Laravel Cloud / Vercel / Netlify. Es Open Source y se instala en tu propio servidor.

#### Requisitos previos
- Servidor con Coolify instalado ([docs.coolify.io](https://docs.coolify.io))
- Dominio apuntando al servidor (DNS A record)
- Repositorio en Git

#### Pasos

##### Paso 1 — Sube el código

```powershell
git init
git add .
git commit -m "feat: dockerize exicompras"
git remote add origin https://github.com/tu-usuario/exicompras.git
git push -u origin main
```

##### Paso 2 — Crea el proyecto en Coolify

1. Entra a tu instancia de Coolify
2. **+ New** → **Resource** → **Application** → **Public/Private Repository** (GitHub)
3. Pega la URL del repo
4. **Build Pack:** selecciona **Dockerfile** (Coolify detecta el `docker-compose.yml` automáticamente)

##### Paso 3 — Configura el dominio

1. En **Domains** → añade tu dominio
2. Coolify configura SSL con Let's Encrypt automáticamente

##### Paso 4 — Configura variables de entorno

En **Environment Variables** del recurso:

```bash
APP_NAME=Exicompras
APP_ENV=production
APP_KEY=base64:PEGA_AQUI_TU_KEY            # genera con: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Database
DB_CONNECTION=mysql
DB_HOST=exicompras-db                       # nombre del servicio en docker-compose
DB_PORT=3306
DB_DATABASE=exicompras
DB_USERNAME=exicompras
DB_PASSWORD=contraseña-segura

# Redis
REDIS_HOST=exicompras-redis
REDIS_PORT=6379

# Cache / Sesiones / Colas
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Storage (Coolify puede provisionar MinIO/S3 automáticamente)
FILESYSTEM_DISK=public

# Mail
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no-reply@tu-dominio.com

# Flag para correr migraciones en el primer deploy
RUN_MIGRATIONS=true
```

##### Paso 5 — Configura el docker-compose

Coolify detecta el `docker-compose.yml` en la raíz. Pero por defecto solo despliega UN servicio (el principal). Para desplegar **todos los servicios** (app, nginx, queue, scheduler, db, redis), tienes dos opciones:

**Opción 1 — Desplegar todo junto (recomendado para empezar):**
1. En Coolify, **+ New** → **Resource** → **Docker Compose**
2. Apunta al repo
3. Coolify usa el `docker-compose.yml` completo
4. El servicio principal (con el dominio) es `nginx`

**Opción 2 — Servicios separados (más control):**
1. Crea un recurso **Application** para `app` (sin puerto público)
2. Crea un recurso **Database** (MySQL) desde Coolify directamente
3. Crea un recurso **Database** (Redis) desde Coolify directamente
4. Crea un recurso **Application** para `nginx` (con el dominio)
5. Crea un recurso **Application** para `queue` (sin puerto)
6. Crea un recurso **Application** para `scheduler` (sin puerto)
7. Conecta los servicios vía la red interna de Coolify

##### Paso 6 — Volúmenes persistentes

En Coolify, ve a **Storages** y añade:
- `app-storage` → `/var/www/html/storage`
- `app-cache` → `/var/www/html/bootstrap/cache`
- `dbdata` → `/var/lib/mysql` (solo si usas el servicio `db` interno)

##### Paso 7 — Despliega

1. Click en **Deploy**
2. Coolify:
   - Clona el repo
   - Construye las imágenes Docker
   - Levanta los servicios
   - Ejecuta el `entrypoint.sh` (migraciones + optimize)
3. Espera ~5-10 minutos

##### Paso 8 — Configura el cron (opcional)

Si no despliegas el servicio `scheduler`, configura un cron en el servidor:
```bash
* * * * * docker exec exicompras-app php artisan schedule:run >> /dev/null 2>&1
```

#### Verificar

Abre tu dominio. La aplicación debería estar funcionando.

---

## 5. Opción B — Despliegue sin Docker (clásico)

### B.1 Local con Laragon / Valet / Sail

#### Con Laragon (Windows — recomendado para este proyecto)

Este proyecto se desarrolla originalmente con Laragon. La configuración es:

1. **Instalar Laragon**: https://laragon.org/download/
2. **Asegurarse de tener PHP 8.3** con extensiones: `intl`, `mbstring`, `gd`, `pdo_mysql`, `bcmath`, `zip`, `exif`, `fileinfo`, `opcache`, `curl`, `openssl`
3. **Habilitar `intl`**: Menu → PHP → Quick settings → `intl`
4. **Habilitar `gd`**: Menu → PHP → Quick settings → `gd`

```powershell
# 1. Clonar dentro de C:\laragon\www
cd C:\laragon\www
git clone https://github.com/tu-usuario/exicompras.git

# 2. Iniciar Laragon (Click en "Start All")

# 3. Visitar http://exicompras.test  (Laragon crea el virtualhost automáticamente)

# 4. En la terminal de Laragon:
cd C:\laragon\www\exicompras
cp .env.example .env
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
```

#### Con Laravel Valet (Mac)

```bash
composer global require laravel/valet
valet install
valet park
cd ~/Sites/exicompras
cp .env.example .env
composer install
npm install && npm run build
php artisan key:generate
php artisan migrate --force --seed
valet secure    # opcional para HTTPS
```

#### Con Laravel Sail (Linux/Docker ligero)

```bash
# Si decides usar Sail (que es Docker pero "ligero"):
php artisan sail:install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --force --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

---

### B.2 Despliegue en Laravel Cloud sin Docker

Laravel Cloud puede desplegar SIN Docker usando **Laravel Cloud Build** (detección automática).

#### Cuándo funciona sin Docker
- Si tu proyecto Laravel **no tiene un `Dockerfile`**, Laravel Cloud usa su build pack automático (Nixpacks)
- En ese caso, el proceso es idéntico al de la sección A.2, pero Laravel Cloud:
  - Detecta `composer.json` y corre `composer install --no-dev`
  - Detecta `package.json` y corre `npm ci && npm run build`
  - Ejecuta el `Procfile` (si existe) o `php artisan serve` por defecto

#### Si quieres forzar el modo "sin Docker" en Laravel Cloud

Simplemente **no commitees el `Dockerfile`**. Laravel Cloud lo saltará y usará su build pack.

```powershell
# .gitignore
Dockerfile
docker-compose.yml
docker/
.dockerignore
```

#### Limitaciones sin Docker en Laravel Cloud
- No puedes personalizar la imagen (instalar extensiones extra, cambiar versiones)
- Dependes del build pack de Laravel Cloud
- Aimeos puede requerir extensiones que no estén en el build pack (aunque suelen estar todas)

---

### B.3 Despliegue en Coolify sin Docker (Nixpacks)

Coolify también soporta **Nixpacks** (similar a Heroku buildpacks).

#### Cuándo usarlo
- Si NO quieres usar Docker
- Si quieres el build pack automático (PHP, Composer, Node, Vite)

#### Configuración
1. En Coolify → **+ New** → **Resource** → **Application**
2. Apunta al repo de Git
3. **Build Pack:** selecciona **Nixpacks** (en vez de Dockerfile)
4. Configura las variables de entorno (igual que en A.3)
5. Configura el dominio
6. Click en **Deploy**

#### Limitaciones
- Menos control sobre el entorno
- Las extensiones PHP disponibles son las del build pack (pueden faltar algunas que Aimeos necesite)
- Más lento de configurar si necesitas personalizaciones

---

### B.4 Despliegue en VPS clásico (Forge-style)

Si tienes tu propio VPS y quieres gestionarlo manualmente (o usar Laravel Forge).

#### Requisitos del servidor
- Ubuntu 22.04+ o Debian 12+
- PHP 8.3 con extensiones: `php8.3-cli php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis php8.3-opcache`
- Nginx 1.24+
- MySQL 8.0+ o MariaDB 10.11+
- Redis 7+ (opcional pero recomendado)
- Composer 2.7+
- Node.js 20+ (para el build)
- Supervisor (para queue workers)

#### Setup manual

```bash
# 1. Instalar dependencias (Ubuntu 22.04)
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql \
                    php8.3-xml php8.3-mbstring php8.3-curl php8.3-zip \
                    php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis \
                    php8.3-opcache nginx mysql-server redis-server \
                    composer nodejs npm supervisor

# 2. Clonar el proyecto
cd /var/www
sudo git clone https://github.com/tu-usuario/exicompras.git
sudo chown -R www-data:www-data exicompras
cd exicompras

# 3. Instalar dependencias
cp .env.example .env
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Configurar Nginx
sudo nano /etc/nginx/sites-available/exicompras
```

#### Configuración de Nginx (sin Docker)

```nginx
server {
    listen 80;
    server_name tudominio.com www.tudominio.com;
    root /var/www/exicompras/public;
    index index.php;

    charset utf-8;
    client_max_body_size 50M;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_read_timeout 300;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

```bash
# 5. Activar el sitio
sudo ln -s /etc/nginx/sites-available/exicompras /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

# 6. Configurar permisos
sudo chown -R www-data:www-data /var/www/exicompras
sudo chmod -R 775 /var/www/exicompras/storage /var/www/exicompras/bootstrap/cache

# 7. Configurar Supervisor (queue workers)
sudo nano /etc/supervisor/conf.d/exicompras-worker.conf
```

#### Configuración de Supervisor

```ini
[program:exicompras-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/exicompras/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/exicompras/worker.log
stopwaitsecs=3600
```

```bash
sudo mkdir -p /var/log/exicompras
sudo chown www-data:www-data /var/log/exicompras
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start exicompras-worker:*
```

#### Configurar Cron (scheduler)

```bash
sudo crontab -e -u www-data
# Añadir:
* * * * * cd /var/www/exicompras && php artisan schedule:run >> /dev/null 2>&1
```

#### SSL con Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d tudominio.com -d www.tudominio.com
```

---

## 6. Post-instalación

### Crear usuario admin de Aimeos

```bash
# Con Docker:
docker compose exec app php artisan aimeos:account

# Sin Docker:
php artisan aimeos:account
```

Sigue las instrucciones para crear el primer administrador (super user) con acceso a `/admin`.

### Poblar datos de ejemplo (opcional, solo dev)

```bash
docker compose exec app php artisan aimeos:setup --option=jobs/admin/job/create-data
```

### Acceder al panel admin

- **Frontend**: `https://tudominio.com`
- **Admin de Aimeos**: `https://tudominio.com/admin`
- **Login con las credenciales** que creaste en `aimeos:account`

---

## 7. Configuración de Aimeos

### Cambiar el tema desde el admin

1. Entra a `https://tudominio.com/admin`
2. **Settings** → pestaña **Basic** → ajusta el **tamaño del logo** con el slider
3. **Settings** → pestaña **Theme** → cambia colores, fuentes, etc.
4. Click en **Save**
5. La extensión `exicompras-theme` bumpea automáticamente `theme_version` para invalidar la caché del navegador

### Cambiar la configuración avanzada (theme vars)

Los presets de tema están en `config/shop.php` bajo `client.html.theme-presets.default`. Puedes sobreescribirlos desde el admin (se guardan en `mshop_locale_site.config['theme/default']`).

Variables disponibles:
- `--ai-primary`, `--ai-secondary`, `--ai-tertiary` — colores de marca
- `--ai-bg`, `--ai-bg-alt` — fondos
- `--ai-nav-bg`, `--ai-nav-text`, `--ai-nav-text-hover` — colores del navbar
- `--ai-nav-logo-height` — altura del logo (32-200px)
- `--bs-menu-bg`, `--bs-menu` — colores del sidebar del admin

---

## 8. Tareas programadas y colas

### Colas

Si despliegas con Docker, el servicio `queue` ya corre `php artisan queue:work` automáticamente. Para escalar:

```bash
docker compose up -d --scale queue=3
```

Sin Docker, usa Supervisor (ver sección B.4).

### Scheduler

Si despliegas con Docker, el servicio `scheduler` corre `schedule:run` cada 60 segundos.

Sin Docker, usa el cron (ver sección B.4).

### Definir tareas programadas

Edita `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

// Ejecutar el setup de Aimeos cada hora
Schedule::command('aimeos:setup')->hourly();

// Limpiar cache de vistas cada día
Schedule::command('view:clear')->daily();
```

---

## 9. HTTPS / SSL

### Con Coolify o Laravel Cloud
SSL se configura automáticamente con Let's Encrypt. Solo necesitas apuntar tu dominio.

### Con Docker en tu propio servidor

Usa [Traefik](https://traefik.io) o [Caddy](https://caddyserver.com) como reverse proxy con auto-SSL.

Ejemplo con Caddy (`Caddyfile`):

```caddy
tudominio.com {
    reverse_proxy exicompras-nginx:80
    encode gzip
}
```

### Con Nginx + Certbot (sin Docker)

```bash
sudo certbot --nginx -d tudominio.com
```

---

## 10. Backups

### Base de datos

#### Con Docker
```bash
# Backup
docker compose exec db mysqldump -uroot -p"$DB_ROOT_PASSWORD" exicompras > backup-$(date +%F).sql

# Restaurar
cat backup-2025-01-15.sql | docker compose exec -T db mysql -uroot -p"$DB_ROOT_PASSWORD" exicompras
```

#### Sin Docker
```bash
mysqldump -u root -p exicompras > backup-$(date +%F).sql
```

### Archivos (uploads)

Los archivos de uploads están en `storage/app/public/`. Con Docker, están en el volumen `app-storage`. Sin Docker, en `/var/www/exicompras/storage/app/public/`.

```bash
# Backup del volumen Docker
docker run --rm -v exicompras_app-storage:/data -v $(pwd):/backup alpine tar czf /backup/storage-$(date +%F).tar.gz /data

# Backup sin Docker
sudo tar czf storage-$(date +%F).tar.gz /var/www/exicompras/storage
```

### Automatizar backups

Añade un cron diario:
```bash
0 3 * * * /opt/exicompras/backup.sh
```

```bash
#!/bin/bash
# /opt/exicompras/backup.sh
BACKUP_DIR=/opt/backups/exicompras
DATE=$(date +%F)
mkdir -p $BACKUP_DIR

docker compose exec -T db mysqldump -uroot -p"$DB_ROOT_PASSWORD" exicompras | gzip > $BACKUP_DIR/db-$DATE.sql.gz
docker run --rm -v exicompras_app-storage:/data -v $BACKUP_DIR:/backup alpine tar czf /backup/storage-$DATE.tar.gz /data

# Subir a S3 (opcional)
aws s3 cp $BACKUP_DIR/db-$DATE.sql.gz s3://mi-bucket/backups/exicompras/
aws s3 cp $BACKUP_DIR/storage-$DATE.tar.gz s3://mi-bucket/backups/exicompras/

# Limpiar backups antiguos (más de 30 días)
find $BACKUP_DIR -mtime +30 -delete
```

---

## 11. Troubleshooting

### Error: "Class 'IntlDateFormatter' not found"

**Causa:** Falta la extensión `intl` de PHP.

**Solución con Docker:**
```dockerfile
# Ya está incluido en el Dockerfile. Reconstruye:
docker compose build --no-cache app
```

**Solución sin Docker:**
```bash
# Ubuntu/Debian
sudo apt install php8.3-intl
sudo systemctl restart php8.3-fpm

# macOS con brew
brew install icu4c
pecl install intl
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Causa:** La app no puede conectar a la base de datos.

**Solución con Docker:**
```bash
# Verificar que el servicio `db` está corriendo
docker compose ps db

# Ver los logs del servicio `db`
docker compose logs db

# Verificar que el host es `db` (no `localhost` o `127.0.0.1`)
grep DB_HOST .env
# Debe decir: DB_HOST=db
```

### Error: "Permission denied" en `storage/` o `bootstrap/cache/`

```bash
# Con Docker
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache

# Sin Docker
sudo chown -R www-data:www-data /var/www/exicompras/storage /var/www/exicompras/bootstrap/cache
sudo chmod -R 775 /var/www/exicompras/storage /var/www/exicompras/bootstrap/cache
```

### Error: "Class 'Aimeos\Shop\Base\View' not found"

**Causa:** Los assets de Aimeos no están publicados.

**Solución:**
```bash
php artisan vendor:publish --tag=public --force
```

### Los assets no se ven actualizados

**Causa:** Caché del navegador o del OPcache.

**Solución:**
```bash
# Con Docker
docker compose exec app php artisan view:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear

# Recompilar assets
docker compose exec app npm run build
```

### El logo no se ve

1. Verifica que la imagen existe en `mshop_locale_site.logo`:
   ```bash
   docker compose exec app php artisan tinker
   > \Aimeos\MShop::create('locale/site')->get('default-site')->getLogo();
   ```
2. Verifica que `public/storage` es un symlink:
   ```bash
   docker compose exec app ls -la public/storage
   ```
3. Si no es un symlink, ejecuta:
   ```bash
   docker compose exec app php artisan storage:link
   ```

### El slider de tamaño de logo no funciona

1. Verifica que `--ai-nav-logo-height` está en `config/shop.php` bajo `theme-presets.default`
2. Limpia caché:
   ```bash
   docker compose exec app php artisan config:clear
   ```
3. Recarga el admin con `Ctrl+Shift+R`

### La cola no procesa jobs

```bash
# Ver logs del worker
docker compose logs -f queue

# Ver jobs fallidos
docker compose exec app php artisan queue:failed

# Reintentar jobs fallidos
docker compose exec app php artisan queue:retry all
```

### Performance lenta

1. **Activa OPcache + JIT** (ya está en el Dockerfile, verifica que `opcache.jit=tracing`)
2. **Usa Redis para cache/sesiones/colas** (en vez de `database`)
3. **Usa S3 para storage** (en vez de local)
4. **Aumenta el `pm.max_children`** en `docker/php/www.conf` (si tienes mucha RAM)
5. **Activa FastCGI cache en Nginx** (avanzado, ver `docker/nginx/nginx.conf`)

### Ver logs en producción

```bash
# Laravel logs
docker compose exec app tail -f storage/logs/laravel.log

# Nginx logs
docker compose logs -f nginx

# PHP-FPM logs (si hay errores fatales)
docker compose exec app tail -f /proc/1/fd/2
```

### Resetear todo (último recurso)

```bash
# ⚠️ ESTO BORRA TODA LA BASE DE DATOS
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan migrate --force --seed
docker compose exec app php artisan aimeos:account
```


---

## 12. Notas de despliegue en Coolify (dockercompose)

> **Contexto:** Este repositorio fue desplegado exitosamente en **Coolify v4.0.0-beta.474** sobre Ubuntu 22 con Traefik v3.6 como proxy. Esta seccion documenta los issues especificos que encontramos y como se resolvieron en el repo, asi no se repiten en futuros deploys.

### 12.1 Compatibilidad de versiones

| Componente | Version minima | Notas |
|---|---|---|
| **PHP** | 8.4 (Aimeos 2025.10) | `aimeos-core 2025.10.x` requiere `php >= 8.4.1` en su `composer.json`; PHP 8.3 falla con `Please provide a valid cache path` por incompatibilidad de plataforma |
| **Composer** | 2.8 (para PHP 8.4) | `composer:2.7` (base del stage `vendor`) tiene PHP 8.3 — usar `2.8` para coherencia |
| **nginx** | 1.27-alpine | El usuario del worker es `nginx` (no `www-data` como en Debian) |
| **Coolify** | 4.0+ | v3.x no soporta `docker_compose_domains` ni el formato de `docker_compose_raw` |

### 12.2 Dockerfile — fixes criticos

#### `composer install` falla en el stage `vendor`

**Sintoma:** `aimeos/aimeos-base 2025.10.2 requires ext-intl * -> it is missing from your system`

**Causa:** El stage `vendor` usa `composer:2.7` (PHP 8.3) que NO tiene `ext-intl`. Aimeos la exige.

**Fix:** Usar `composer update` (no `install`) con `--ignore-platform-req=*` en ese stage, ya que el runtime stage (`php:8.4-fpm-bookworm`) SI tiene las extensiones.

```dockerfile
RUN composer update \
        --no-dev \
        --with-all-dependencies \
        --ignore-platform-req=* \
        ...
```

> **Por que `update` y no `install`?** El `composer.lock` que viene en el repo estaba desincronizado con `aimeos-core 2025.10.4` (resoluble solo via `update --with-all-dependencies`).

#### `docker-php-ext-install gd` no compila con JPEG/WebP/Freetype

**Sintoma:** `Intervention\Image\Drivers\Gd\Encoders\imagewebp()` undefined al subir un logo desde el admin.

**Causa:** El `docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype` solo actualiza los flags del build, pero `docker-php-ext-install gd` (corrido antes) **no recompila**. Resultado: `php -r 'var_dump(gd_info())'` muestra `JPEG Support: false`, `WebP Support: false`.

**Fix:** Combinar `configure + install` en el mismo `RUN`:

```dockerfile
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" gd
```

#### Extension `pdo_sqlite` requiere `libsqlite3-dev`

**Sintoma:** `configure: error: Package requirements (sqlite3 >= 3.7.7) were not met`

**Fix:** Agregar `libsqlite3-dev` al `apt-get install` block:

```dockerfile
RUN apt-get update && apt-get install -y --no-install-recommends \
        ... \
        libsqlite3-dev \
        tzdata \
```

#### `.dockerignore` excluye `docker/` pero el Dockerfile lo necesita

**Sintoma:** `failed to solve: "/docker/entrypoint.sh": not found`

**Causa:** El `.dockerignore` del repo tiene `docker/` excluido (asumiendo que el compose monta por bind-mount los archivos de `docker/`). Pero al hacer `docker compose build` en Coolify, el contexto de build es el directorio del repo y necesita todos los archivos del Dockerfile (`docker/entrypoint.sh`, `docker/php/*.ini`, etc.).

**Fix:** Quitar la linea `docker/` del `.dockerignore` (no tocar `Dockerfile`/`docker-compose.yml`/`docker-compose.*.yml` que siguen excluidos).

### 12.3 docker-compose.yml — fixes criticos

#### `command: []` falla el validador de Coolify

**Sintoma:** `validating /artifacts/.../docker-compose.yml: services.queue.command must be a null`

**Fix:** Cambiar `command: []` a `command: ~` (YAML para `null`).

#### Bind-mounts a `/etc/nginx/nginx.conf` fallan con overlay2

**Sintoma:** `failed to set up container: failed to create shim task: ... cannot create subdirectories in ".../merged/etc/nginx/nginx.conf": not a directory`

**Causa:** Alguna incompatibilidad entre el driver `overlay2` de Docker y la imagen `nginx:1.27-alpine` cuando se bind-mountea sobre `/etc/nginx/*.conf`. (El error ocurre incluso montando un archivo regular sobre `/etc/nginx/nginx.conf`.)

**Fix:** Crear un Dockerfile dedicado para nginx que bakee las configs:

```dockerfile
# docker/nginx/Dockerfile
FROM nginx:1.27-alpine
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public/vendor /var/www/html/public/vendor
COPY public/css /var/www/html/public/css
...
```

Y referenciarlo desde el compose:

```yaml
nginx:
  build:
    context: .                    # contexto en root del repo
    dockerfile: docker/nginx/Dockerfile
```

> **Nota:** El `context: .` permite que el Dockerfile acceda a `public/vendor`, `public/css`, etc. desde el root del repo. Esos assets se sirven directamente desde nginx (sin roundtrip a PHP-FPM), acelerando la primera carga.

#### `app-storage` montado en `/var/www/html/storage` borra subdirs

**Sintoma:** `Please provide a valid cache path.` desde PHP-FPM. `storage/framework/` solo tiene `.gitignore` y `storage/framework/views/` no existe.

**Causa:** El compose original monta `app-storage:/var/www/html/storage` (sobre el directorio entero), lo que **reemplaza** el contenido del directorio `storage` en la imagen — incluyendo los subdirectorios que Laravel espera (`framework/cache`, `framework/sessions`, `framework/views`, etc.).

**Fix:** Montar `app-storage` en `/var/www/html/storage/app` (solo user uploads), no en todo `/storage`. Tambien eliminar el volumen `app-cache` (no es stateful, Laravel lo regenera).

```yaml
volumes:
  - 'app-storage:/var/www/html/storage/app'    # solo uploads del usuario
  # - 'app-cache:/var/www/html/bootstrap/cache' # ELIMINADO
```

Adicionalmente, el `entrypoint.sh` debe crear los subdirs faltantes si no existen:

```bash
mkdir -p /var/www/html/storage/framework/{cache/data,sessions,testing,views}
chown -R www-data:www-data /var/www/html/storage
```

#### Conflicto de puertos host con Coolify

**Sintoma:** `Bind for 0.0.0.0:3306 failed: port is already allocated` y `0.0.0.0:80`.

**Causa:** Coolify ya usa los puertos 80/443 (Traefik) y 3306 (MySQL de Coolify si esta habilitado).

**Fix:** Quitar los `ports:` a host del compose. Coolify enruta via Traefik labels (red `coolify`); los containers solo necesitan escuchar en `0.0.0.0` interno.

```yaml
# Eliminar del compose:
nginx:
  ports:
    - "${HTTP_PORT:-80}:80"     # ❌ ELIMINADO
    - "${HTTPS_PORT:-443}:443"  # ❌ ELIMINADO
db:
  ports:
    - "${DB_PORT:-3306}:3306"   # ❌ ELIMINADO
```

#### Traefik labels automaticos para Coolify

**Sintoma:** Las requests a `https://<app>.sslip.io` devuelven `404 page not found` de Traefik, aun con los labels correctos en el container.

**Causa:** El container nginx esta en dos redes (`coolify` y la del proyecto). Sin `traefik.docker.network=coolify`, Traefik elige una red arbitraria para resolver el service URL y falla.

**Fix:** Agregar la label `traefik.docker.network=coolify` y prioridad alta:

```yaml
nginx:
  labels:
    - "traefik.enable=true"
    - "traefik.docker.network=coolify"
    - "traefik.http.routers.exicompras-nginx.rule=Host(`<tu-dominio>`)"
    - "traefik.http.routers.exicompras-nginx.entryPoints=http"
    - "traefik.http.routers.exicompras-nginx.priority=100"
    - "traefik.http.services.exicompras-nginx.loadbalancer.server.port=80"
    - "coolify.managed=true"
    - "coolify.type=application"
```

> **Tambien:** el container nginx debe estar conectado a la red `coolify`:
> ```yaml
> networks:
>   - exicompras
>   - coolify      # ← necesario para Traefik
> ```

### 12.4 nginx — fixes criticos

#### `user www-data;` no existe en alpine

**Sintoma:** `getpwnam("www-data") failed in /etc/nginx/nginx.conf`

**Fix:** Cambiar a `user nginx;` (default de `nginx:1.27-alpine`).

#### `try_files $uri =404` rompe el flujo a fastcgi

**Sintoma:** `File not found.` desde PHP-FPM (no Laravel), o `404 page not found` desde nginx directamente.

**Causa:** El `try_files $uri =404` en el bloque `location ~ \.php$` chequea si el archivo existe en el filesystem local del container nginx. Como nginx no tiene los archivos PHP (viven en el container `app`), siempre devuelve 404 antes de llamar a fastcgi.

**Fix:** Quitar el `try_files` del bloque PHP:

```nginx
location ~ \.php$ {
    fastcgi_split_path_info  ^(.+\.php)(/.+)$;
    fastcgi_pass             app:9000;
    fastcgi_index            index.php;
    fastcgi_param            SCRIPT_FILENAME  $document_root$fastcgi_script_name;
    fastcgi_param            DOCUMENT_ROOT    $document_root;
    include                   fastcgi_params;
}
```

#### `$realpath_root` se evalua a cadena vacia

**Sintoma:** `File not found.` con `SCRIPT_FILENAME=/index.php` en PHP-FPM (en lugar de `/var/www/html/public/index.php`).

**Causa:** `$realpath_root` ejecuta `realpath($document_root)`. Como `/var/www/html/public` no existe en el container nginx, devuelve `false` (cadena vacia en nginx), entonces `SCRIPT_FILENAME = "" + "/index.php" = "/index.php"`.

**Fix:** Usar `$document_root` (literal) en vez de `$realpath_root`:

```nginx
fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
fastcgi_param  DOCUMENT_ROOT    $document_root;
```

#### Regex location gana sobre prefix location para assets

**Sintoma:** `404` en `GET /vendor/shop/themes/default/style.css`.

**Causa:** nginx prioriza `location ~* \.(css|js)$` (regex) sobre `location /vendor/shop/` (prefix). La regex hace `try_files $uri =404` que falla localmente.

**Fix:** Marcar los prefix locations de Aimeos con `^~`:

```nginx
location ^~ /vendor/shop/   { try_files $uri $uri/ /index.php?$query_string; }
location ^~ /packages/      { try_files $uri $uri/ /index.php?$query_string; }
location ^~ /admin/         { try_files $uri $uri/ /index.php?$query_string; }
```

### 12.5 Aimeos — requisitos especificos

#### `php artisan migrate` no es suficiente

**Sintoma:** `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'exicompras.mshop_locale_site' doesn't exist`

**Causa:** Aimeos tiene su propio sistema de migrations separado (`vendor/aimeos/aimeos-core/setup/*.php`) que NO se ejecutan con `php artisan migrate`. Solo `aimeos:setup` los corre.

**Fix:** Agregar al `entrypoint.sh` despues de `migrate`:

```bash
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "🗄️  Ejecutando migraciones Laravel..."
    php artisan migrate --force --no-interaction
    echo "🗄️  Ejecutando setup Aimeos..."
    php artisan aimeos:setup --no-interaction 2>&1 | tail -5
fi
```

#### Faltan archivos de configuracion Laravel

**Sintoma:** `Please provide a valid cache path` (aunque los subdirs de storage existen) — `config('view.compiled')` evalua a `false` (string vacio).

**Causa:** El repo no incluye `config/view.php` (raro pero pasa).

**Fix:** Agregar `config/view.php` al repo:

```php
<?php
return [
    'paths' => [resource_path('views')],
    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),
];
```

> **Nota:** `realpath()` en el `compiled` original falla si el dir no existe — usar `storage_path()` directo.

### 12.6 Workflow de primer deploy

Para un deploy desde cero en Coolify con este repo:

1. **Crear deploy key en Coolify** y subir la publica como deploy key en GitHub (read-only)
2. **Crear la aplicacion** en Coolify como `dockercompose` con:
   - `project_uuid`: del proyecto donde vivira la app
   - `server_uuid`: el server donde corre Coolify
   - `private_key_uuid`: la deploy key SSH
   - `git_repository`: `git@github.com:<owner>/<repo>.git`
   - `git_branch`: `main`
   - `build_pack`: `dockercompose`
   - `docker_compose_location`: `/docker-compose.yml` (con leading `/`)
   - `ports_exposes`: `80`
3. **Setear env vars** via API:
   ```
   APP_KEY, DB_DATABASE, DB_USERNAME, DB_PASSWORD, DB_ROOT_PASSWORD,
   DB_HOST=db, REDIS_HOST=redis, DB_CONNECTION=mysql,
   CACHE_STORE=redis, SESSION_DRIVER=redis, QUEUE_CONNECTION=redis,
   RUN_MIGRATIONS=true, APP_ENV=production (runtime only),
   APP_DEBUG=false, HTTP_PORT=80
   ```
4. **Trigger deploy** (`POST /api/v1/applications/{uuid}/start`)
5. **Post-deploy manual** (una sola vez): `php artisan aimeos:setup` dentro del container app (o esperar al `entrypoint.sh` con `RUN_MIGRATIONS=true`)

### 12.7 Checklist post-deploy

- [ ] `GET /up` → HTTP 200 (healthcheck Laravel)
- [ ] `GET /` → HTTP 200 (~30 KB HTML)
- [ ] `GET /css/exinavbar.css` → HTTP 200, `Content-Type: text/css`
- [ ] `GET /js/exinavbar.js` → HTTP 200, `Content-Type: application/javascript`
- [ ] `GET /vendor/shop/themes/default/aimeos.css` → HTTP 200
- [ ] Containers `app`, `nginx`, `queue`, `scheduler`, `db`, `redis` en estado `healthy`/`running`
- [ ] `php artisan aimeos:setup` ejecutado al menos una vez (crea tablas `mshop_*`)
- [ ] Login en `/admin` funciona con el usuario `superuser=1`

---

## 📚 Referencias

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Aimeos 2025.10 Docs](https://aimeos.org/docs/2025.x/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Laravel Cloud](https://cloud.laravel.com/docs)
- [Coolify Docs](https://docs.coolify.io)

---

**¿Necesitas ayuda?** Abre un issue en GitHub o consulta la documentación de Aimeos.
