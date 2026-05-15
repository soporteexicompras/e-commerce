# Exicompras — Marketplace Multi-Vendor

Tienda marketplace construida con **Laravel 12** y **Aimeos 2025.10**.  
Permite que múltiples vendedores publiquen sus propios productos y que los clientes compren de varios vendedores en un único carrito y checkout.

---

## Requisitos del entorno

| Componente | Versión mínima |
|---|---|
| PHP | 8.2+ (probado con 8.3.30) |
| Composer | 2.1+ |
| MySQL | 5.7.8+ / 8.x (probado con 8.4.3) |
| Node.js | 18+ (para compilar assets frontend) |

---

## Levantar el entorno localmente

### 1. Clonar / descomprimir el proyecto

```bash
cd C:\laragon\www
# El proyecto ya está en la carpeta Exicompras/
cd Exicompras
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Configurar variables de entorno

Copia `.env.example` a `.env` si no existe y ajusta las credenciales MySQL:

```bash
copy .env.example .env
php artisan key:generate
```

Variables clave en `.env`:

```env
APP_NAME=Exicompras
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=exicompras
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file

SHOP_MULTISHOP=true
SHOP_REGISTRATION=true
```

### 4. Crear la base de datos

```sql
CREATE DATABASE IF NOT EXISTS exicompras
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### 5. Ejecutar migraciones

```bash
php artisan migrate
```

### 6. Inicializar Aimeos (tablas + datos base)

```bash
# Sin datos demo (estructura vacía):
php artisan aimeos:setup

# Con datos demo (productos y categorías de ejemplo):
php artisan aimeos:setup --option=setup/default/demo:1
```

### 7. Compilar assets frontend

```bash
npm install
npm run build
```

### 8. Crear usuarios de prueba

```bash
# Admin principal (acceso total):
php artisan aimeos:account --super admin@exicompras.com

# Vendedor (acceso editor, solo a sus datos):
php artisan aimeos:account --editor seller@exicompras.com
```

> En Windows con Laragon, si el comando no acepta contraseña por stdin,
> usa el script de conveniencia incluido:
> ```bash
> php create_accounts.php
> php assign_seller_role.php
> ```

### 9. Arrancar el servidor de desarrollo

```bash
php artisan serve
```

---

## URLs de acceso

| URL | Descripción |
|---|---|
| `http://127.0.0.1:8000/` | Página principal de la tienda (catálogo home) |
| `http://127.0.0.1:8000/shop/search` | Listado de productos |
| `http://127.0.0.1:8000/admin` | Panel de administración principal (owner) |
| `http://127.0.0.1:8000/admin/default/jqadm/dashboard` | Dashboard admin Aimeos |
| `http://127.0.0.1:8000/register` | Registro de clientes nuevos |
| `http://127.0.0.1:8000/login` | Login de usuarios |
| `http://127.0.0.1:8000/profile` | Perfil / cuenta del cliente |

---

## Credenciales de prueba

### Admin principal (owner)

| Campo | Valor |
|---|---|
| Email | `admin@exicompras.com` |
| Contraseña | `Admin2024!` |
| Acceso | Panel `/admin` — control total |

### Vendedor de prueba

| Campo | Valor |
|---|---|
| Email | `seller@exicompras.com` |
| Contraseña | `Seller2024!` |
| Acceso | Panel `/admin` — solo sus productos (rol `editor`) |

---

## Flujo de usuarios

### Registro de un cliente

1. Ir a `http://127.0.0.1:8000/register`
2. Rellenar nombre, email y contraseña
3. Hacer clic en **Register**
4. El usuario queda registrado como cliente normal
5. Puede comprar productos, ver historial de pedidos en `/profile`

### Cómo un cliente se convierte en vendedor

En Aimeos, el proceso de conversión cliente → vendedor se gestiona desde el panel de administración:

1. El **admin principal** accede a `/admin`
2. Va a **Customers** → busca al cliente
3. En la pestaña **Groups**, asigna el grupo `editor`
4. Opcionalmente, si se usa multi-site (`SHOP_MULTISHOP=true`), el admin puede:
   - Crear un nuevo **Site** para el vendedor en **Localization → Sites**
   - Asignar ese site al usuario para que tenga su propio espacio aislado

Alternativamente, el vendedor puede auto-registrarse con rol editor via:

```bash
php artisan aimeos:account --editor nuevo_vendedor@email.com
```

### Cómo el vendedor gestiona sus productos

1. El vendedor accede a `http://127.0.0.1:8000/login` con sus credenciales
2. Después del login, navega a `http://127.0.0.1:8000/admin`
3. Ve el panel Aimeos restringido a su propio site/datos
4. Desde el panel puede:
   - **Products** → Crear, editar, clonar productos
   - **Products → Media** → Subir fotos de productos
   - **Products → Prices** → Configurar precios y bloques de precio
   - **Products → Stock** → Gestionar stock por almacén
   - **Products → Texts** → Añadir descripciones, títulos, SEO
   - **Catalog** → Organizar productos en categorías
   - **Suppliers** → Gestionar su perfil de vendedor

---

## Arquitectura multi-vendor

Aimeos implementa multi-vendor mediante un sistema de **Sites** (sitios):

- **Site `default`**: el site principal de Exicompras (owner)
- **Sites hijos**: cada vendedor puede tener su propio sub-site
- Los usuarios con rol `editor` solo ven y gestionan los datos de su site asignado
- Los usuarios con rol `admin` ven todos los sites bajo el suyo
- Los usuarios `superuser` ven y gestionan absolutamente todo

El carrito y el checkout son compartidos: un cliente puede agregar productos de múltiples vendedores y pagar en un único proceso de pago.

---

## Comandos clave ejecutados durante la instalación

```bash
# 1. Crear proyecto Laravel
composer create-project laravel/laravel Exicompras

# 2. Agregar Aimeos al composer.json y actualizar
composer update -W

# 3. Instalar Laravel Breeze (autenticación)
php artisan breeze:install blade
npm install && npm run build

# 4. Publicar configuración y assets de Aimeos
php artisan vendor:publish --tag=config --tag=public

# 5. Migraciones Laravel (tablas users, cache, jobs)
php artisan migrate

# 6. Setup Aimeos (todas las tablas del shop)
php artisan aimeos:setup

# 7. Crear admin superuser
php artisan aimeos:account --super admin@exicompras.com

# 8. Crear vendedor con rol editor
php artisan aimeos:account --editor seller@exicompras.com
```

---

## Estructura del proyecto

```
Exicompras/
├── app/
│   ├── Models/User.php          # Modelo usuario (con campo superuser)
│   └── Providers/
│       └── AppServiceProvider.php  # Gate::define('admin') para Aimeos
├── config/
│   └── shop.php                 # Configuración principal de Aimeos
├── public/
│   ├── aimeos/                  # Media de productos subidos
│   └── vendor/shop/             # Temas y assets de Aimeos
├── resources/
│   └── views/                   # Vistas Blade (Breeze + Aimeos)
├── routes/
│   └── web.php                  # Rutas (home → Aimeos CatalogController)
└── .env                         # Variables de entorno
```

---

## Limitaciones conocidas / pendiente

- **Pagos**: Aimeos soporta 100+ pasarelas de pago (PayPal, Stripe, etc.) pero requieren configuración adicional en `config/shop.php` bajo `mshop/service`.
- **Emails**: configurados en modo `log` (desarrollo). Para producción, configurar SMTP en `.env`.
- **SHOP_REGISTRATION**: La variable `SHOP_REGISTRATION=true` en `.env` habilita el registro en Aimeos. El flujo de auto-solicitud de rol vendedor puede requerir lógica de negocio personalizada (formulario + aprobación del admin).
- **Multi-site para vendedores**: la creación automática de un sub-site por cada vendedor nuevo requiere un evento/listener personalizado en Laravel que llame al API de Aimeos.
- **Producción**: configurar `APP_DEBUG=false`, `SESSION_DRIVER=redis` o `file`, cache de configuración con `php artisan config:cache`.
