# ARCHITECTURE.md — Arquitectura del sistema

> Vista logica del proyecto: capas, modulos, como viaja una request desde el navegador hasta la BD.
> Complementa AGENTS.md (convenciones) y CUSTOMIZATIONS.md (codigo custom).

---

## 1. Vista general por capas

```
                  Navegador (Blade renderizado + Alpine.js)
                          |
                          v
        +-----------------+-----------------+
        |                                   |
        v                                   v
  Rutas Laravel (web.php,              Rutas Aimeos (cargadas por
  auth.php)                            Aimeos\Shop\Route en
  - /, /profile/me, /favorites/*      bootstrap del paquete)
                                      - /shop/*, /admin/*, /jsonapi/*
                                          |
                                          v
        +-----------------+-----------------+
        |                                   |
        v                                   v
  Controllers propios                  Controllers Aimeos
  (App\Http\Controllers\)               (Aimeos\Shop\Controller\*)
  - ProfileController                   - CatalogController
  - FavoriteController                  - BasketController, CheckoutController
  - Auth\* (Breeze)                     - Admin\JQAdm\* (admin)
        |                                   |
        +-----------------+-----------------+
                          |
                          v
                   Models / Services
                          |
        +-----------------+-----------------+
        |                                   |
        v                                   v
   BD Laravel (users, sessions,       BD Aimeos (mshop_*,
   cache, jobs, favorites)            madmin_*)
```

Ambas ramas (Laravel y Aimeos) comparten **una sola BD MySQL**. La distincion es de capas, no de almacenamiento.

---

## 2. Modulos de la aplicacion

| Modulo | Origen | Carpeta raiz | Notas |
|---|---|---|---|
| Auth + perfil de usuario | Laravel Breeze | `app/Http/Controllers/Auth/`, `resources/views/auth/`, `resources/views/profile/` | Perfil movido a `/profile/me` (ver CUSTOMIZATIONS §8) |
| Wishlist / Favoritos | Custom | `app/Models/Favorite.php`, `app/Http/Controllers/FavoriteController.php` | Soporta guest + auth (ver CUSTOMIZATIONS §1) |
| Catalogo publico (home, listado, detalle) | Aimeos + overrides | `resources/views/vendor/shop/catalog/` | Overrides en `vendor/shop/` (ver CUSTOMIZATIONS §10) |
| Busqueda y filtros | Aimeos | Overrides en `ext/exicompras-theme/templates/client/html/catalog/filter/` | Filtros: precio, supplier, atributos |
| Carrito y checkout | Aimeos | `resources/views/vendor/shop/account/`, Aimeos\Controllers\Basket\* | Multi-vendor nativo (consolida productos de varios sites en un solo carrito) |
| Panel admin (JQAdm) | Aimeos | `resources/views/vendor/shop/jqadm/`, overrides en `ext/exicompras-theme/templates/admin/jqadm/` | Subpart `Settings\Theme` propio |
| Cuenta de cliente (historial, etc.) | Aimeos | `resources/views/vendor/shop/account/` | Config en `config/shop.php` `page.account-index` |
| Importador de catalogo demo | Custom (Artisan) | `app/Console/Commands/ImportProducts.php` | Seed de `public/Productos de prueba/` |
| Extensiones Aimeos | Custom | `ext/exicompras-theme/` | Solo hay una por ahora |

---

## 3. Mapa de rutas principales

Definidas en `routes/web.php` (Laravel) + las que Aimeos publica automaticamente al bootear.

### Rutas Laravel propias (`routes/web.php`)

| Verbo + Path | Nombre | Controller | Auth |
|---|---|---|---|
| `GET /` | `aimeos_home` | `Aimeos\Shop\Controller\CatalogController@homeAction` | No |
| `GET /dashboard` | `dashboard` | closure (redirige por rol) | Si |
| `GET /profile/me` | `profile.edit` | `ProfileController@edit` | Si |
| `PATCH /profile/me` | `profile.update` | `ProfileController@update` | Si |
| `DELETE /profile/me` | `profile.destroy` | `ProfileController@destroy` | Si |
| `GET /favorites` | `favorites.index` | `FavoriteController@index` | Opcional |
| `POST /favorites` | `favorites.store` | `FavoriteController@store` | Opcional |
| `DELETE /favorites/{product}` | `favorites.destroy` | `FavoriteController@destroy` | Opcional |
| `POST /favorites/sync` | `favorites.sync` | `FavoriteController@sync` | Si |
| Auth (login, register, etc.) | varios | `App\Http\Controllers\Auth\*` | Mix |

### Rutas Aimeos (auto-cargadas)

| Prefijo | Proposito |
|---|---|
| `/shop/*` | Tienda publica (catalogo, busqueda, detalle) |
| `/admin/{site}/jqadm/*` | Panel JQAdm (requiere auth + rol) |
| `/admin/{site}/graphql` | GraphQL admin |
| `/admin/{site}/jsonadm/*` | API JSON admin |
| `/jsonapi/*` | API JSON publica |
| `/s/{supplier}` | Pagina publica de vendedor (Supplier) |
| `/p/{page}` | CMS pages |

Los prefijos exactos se configuran en `config/shop.php` -> `routes.*` (hoy todos comentados = defaults Aimeos).

---

## 4. Ciclo de vida de una request

### 4.1 Request al frontend publico (ej. `GET /shop/list`)

1. **Nginx** (o `php artisan serve`) -> `public/index.php`.
2. **Kernel HTTP** -> middlewares (`web`, CSRF, sesion, locale).
3. **Router** -> matchea ruta Aimeos -> `Aimeos\Shop\Controller\CatalogController`.
4. **Controller** -> obtiene `Context` Aimeos (`app('aimeos.context')->get()`), resuelve criterios de busqueda.
5. **Managers Aimeos** (`MShop\Product\Manager\Standard`, etc.) -> consulta `mshop_*` aplicando `siteid` automaticamente.
6. **Templates** -> resuelve la vista segun orden:
   - `ext/<ext>/templates/...`
   - `resources/views/vendor/shop/...`
   - `vendor/aimeos/.../templates/...`
7. **Render** -> HTML con CSS/JS inyectado (Tailwind + Alpine + Aimeos CSS + custom theme).

### 4.2 Request al panel admin (ej. `GET /admin/default/jqadm/product`)

1-3. Igual que arriba.
4. **Auth** -> `auth` middleware; el Gate `admin` decide si el usuario pasa.
5. **JQAdm Dispatcher** -> resuelve el recurso (`product`) y el subpart (default `text`).
6. **Subpart classes** (`Aimeos\Admin\JQAdm\Product\Text\Standard` o la override `Theme\Exicompras` si aplica).
7. **Managers Aimeos** -> lectura/escritura segun accion.
8. **Template** -> resuelto con prioridad de ext.

### 4.3 Request a favoritos (`POST /favorites`)

1-3. Igual que arriba.
4. **`FavoriteController::store`** -> valida input (Form Request implicita).
5. **Tabla `favorites`** (BD Laravel, no Aimeos) -> inserta fila.
6. **Respuesta**: JSON `{count, ok}` si la request es AJAX, redirect con flash si es navegador.

---

## 5. Dependencias externas

| Servicio | Uso | Config |
|---|---|---|
| MySQL 8.x | BD unica (Laravel + Aimeos) | `.env` `DB_*` |
| Filesystem local | `public/aimeos/*` (media de productos) | `config/shop.php` `resource.fs-media` |
| Redis (recomendado prod) | Cache, sesiones, colas | `.env` `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION` |
| Mail (SMTP / log / Resend) | Confirmaciones, recuperacion | `.env` `MAIL_*` |

Hoy **ninguna pasarela de pago** esta configurada. Aimeos soporta 100+ via `config/shop.php` -> `mshop/service`.

---

## 6. Multi-tenant (multi-site)

Activado con `SHOP_MULTISHOP=true` en `.env`.

```
                +-----------------------+
                | Site "default" (1.)   |  superuser / admin
                +----------+------------+
                           |
        +------------------+------------------+
        |                  |                  |
   +----v-----+       +----v-----+       +----v-----+
   | site-a   |       | site-b   |       | site-c   |
   | (1.a.)   |       | (1.b.)   |       | (1.c.)   |
   | editor   |       | editor   |       | editor   |
   +----------+       +----------+       +----------+
```

- **siteid** aparece en casi todas las tablas `mshop_*` (`1.`, `1.a.`, etc.).
- Los **Managers Aimeos** aplican `siteid` automaticamente al contexto.
- **Carrito y checkout** consolidan productos de varios sites para el `customer` (que no tiene site propio).
- El **editor** solo ve productos de su site asignado.
- El **admin** ve su site + descendientes.
- El **superuser** lo ve todo.

Ver AGENTS.md §3 para el detalle de roles.

---

## 7. Comunicacion entre capas

| Origen | Destino | Mecanismo |
|---|---|---|
| Controller Laravel | Modelo Eloquent | Eloquent directo |
| Controller Laravel | Tabla Aimeos | `Aimeos\MShop::create($ctx, 'product')` o, si es caso one-off, `DB::table('mshop_*')` con `siteid` explicito |
| Template Blade | Datos | Composers / `view()->share()` / props |
| Aimeos event `order.checkout.*` | Listener Laravel | `app/Listeners/*` (futuro) |
| Custom artisan command | Aimeos context | `app('aimeos.context')->get(false)` |

---

## 8. Puntos de extension oficiales (ordenados por prioridad)

1. **Extension propia en `ext/<nombre>/`** (preferido para todo lo que toca Aimeos).
2. **Overrides Blade en `resources/views/vendor/shop/`** (templates concretos).
3. **Listeners / Events** para desacoplar side-effects.
4. **Managers custom** extendiendo los de Aimeos (casos raros).

**Nunca** modificar `vendor/aimeos/...` ni `vendor/laravel/...`.

---

## 9. Como encaja una feature nueva

Ejemplo: anadir "lista de deseos compartida".

1. Decide si toca Aimeos o no. Si no -> modelo + Controller + migracion Laravel propios (estilo `Favorite`).
2. Si toca Aimeos -> ext nueva en `ext/<nombre>/`.
3. Registra la ruta en `routes/web.php` (Laravel) o configura el prefijo en `config/shop.php` (Aimeos).
4. Documenta en `docs/CUSTOMIZATIONS.md`.
5. Si requiere decision arquitectonica (ej. nuevo storage, nuevo provider de auth) -> ADR en `docs/adr/`.
6. Tests en `tests/Feature/*` y `tests/Unit/*`.