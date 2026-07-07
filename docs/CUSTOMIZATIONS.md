# CUSTOMIZATIONS.md — Inventario de código propio

> Catálogo de toda la lógica que **no es de fábrica** ni de Aimeos ni de Laravel.
> Antes de añadir una feature, revisa aquí para evitar duplicar o romper algo existente.
> Cualquier personalizacion nueva debe documentarse en este archivo.

---

## Indice rapido

| # | Feature | Tipo | Archivos clave |
|---|---|---|---|
| 1 | Wishlist / Favoritos | Modelo + Controller + tabla propia | `app/Models/Favorite.php`, `app/Http/Controllers/FavoriteController.php` |
| 2 | Extension `exicompras-theme` | Extension Aimeos (`ext/`) | `ext/exicompras-theme/` |
| 3 | Subpart `Settings\Theme` propio | Override JQAdm | `ext/.../src/Admin/JQAdm/Settings/Theme/Exicompras.php` |
| 4 | Theme presets (CSS vars) | Config Aimeos | `config/shop.php` `client.html.theme-presets.default` |
| 5 | Gate `admin` para Aimeos | Service Provider | `app/Providers/AppServiceProvider.php` |
| 6 | Helper `User::hasAimeosGroup()` | Modelo Eloquent | `app/Models/User.php` |
| 7 | Redireccion post-login por rol | Controller Breeze | `app/Http/Controllers/Auth/AuthenticatedSessionController.php` |
| 8 | Ruta de perfil movida a `/profile/me` | Rutas + vistas Breeze | `routes/web.php`, `resources/views/profile/` |
| 9 | Restricciones JQAdm `customer` / `users` | Config Aimeos | `config/shop.php` `admin.jqadm.resource.*` |
| 10 | Overrides de plantillas Aimeos | Vistas Blade | `resources/views/vendor/shop/` |
| 11 | Comando `exi:import-products` | Artisan command | `app/Console/Commands/ImportProducts.php` |
| 12 | Drawer + icono wishlist en parciales | Blade parcials | `resources/views/vendor/shop/partials/exi-*.blade.php` |
| 13 | Sugerencias automatizadas (`aimeos:jobs`) | Hook en comando import | `ImportProducts.php` (linea 56) |

---

## 1. Wishlist / Favoritos

Estado: **funcional y estable**. Soporta guests (session_id) y autenticados (user_id) con sincronizacion manual.

### Componentes

| Capa | Archivo | Notas |
|---|---|---|
| Modelo | `app/Models/Favorite.php` | Eloquent, scopes `ForUser`, `ForSession`, `Current` |
| Controller | `app/Http/Controllers/FavoriteController.php` | CRUD + endpoint `sync` |
| Migracion | `database/migrations/2026_07_01_142500_create_favorites_table.php` | Tabla propia `favorites` |
| Rutas | `routes/web.php` (linea 27-32) | Prefijo `/favorites`, accesible sin auth salvo `sync` |
| Vista principal | `resources/views/vendor/shop/account/favorites.blade.php` | Override de `shop::account.favorites` |
| Parciales | `resources/views/vendor/shop/partials/exi-wishlist-icon.blade.php`, `exi-drawer.blade.php` | Icono + drawer lateral |

### Rutas

```php
GET    /favorites              // vista de lista (auth opcional)
POST   /favorites              // anade producto (JSON o redirect)
DELETE /favorites/{product}    // elimina producto
POST   /favorites/sync         // mueve favoritos guest -> user_id (requiere auth)
```

### Mecanismo de identificacion

- **Guest**: cookie + sesion `exi_fav_id` (string random 32 chars, expira 30 dias).
- **Autenticado**: `user_id` directa.
- **Union de ambos**: el scope `Current` mira primero auth, luego sesion.

### Punto de friccion conocido

`Favorite::syncOnLogin()` **no se llama automaticamente** al hacer login.
Solo se invoca cuando el frontend hace `POST /favorites/sync`.
Si se quiere automatico, hay que engancharlo en `AuthenticatedSessionController::store()` despues de `authenticate()`.

---

## 2. Extension `exicompras-theme`

Extension Aimeos declarada en `ext/exicompras-theme/manifest.php`. Depende de `ai-admin-jqadm`.

| Pieza | Proposito |
|---|---|
| `manifest.php` | Declara paths de templates que override |
| `src/Admin/JQAdm/Settings/Theme/Exicompras.php` | Extiende `Standard`, bump `theme_version` al guardar |
| `templates/admin/jqadm/page.php` | Override del layout del panel admin |
| `templates/admin/jqadm/settings/item.php` | Override del formulario de Settings |
| `templates/client/html/catalog/filter/body.php` | Override del filtro lateral de catalogo |
| `templates/client/html/catalog/filter/price-body.php` | Filtro de precios |
| `templates/client/html/catalog/filter/supplier-body.php` | Filtro por vendedor |

Activacion: `config/shop.php` -> `admin.jqadm.settings.theme.name = 'Exicompras'`.

### Por que `theme_version`

Al guardar el tema en el admin, este increment fuerza la invalidacion de la cache de CSS/JS en el navegador (cache-busting). Sin esto, los visitantes ven el tema viejo hasta hard-refresh.

---

## 3. Subpart `Settings\Theme` propio

Clase: `Aimeos\Admin\JQAdm\Settings\Theme\Exicompras` (en `ext/exicompras-theme/src/...`).

```php
class Exicompras extends Standard
{
    public function save() : ?string
    {
        $result = parent::save();                    // logica nativa
        $site   = $this->context()->locale()->getSiteItem();
        $curr   = (int) $site->getConfigValue('theme_version', 0);
        $site->setConfigValue('theme_version', $curr + 1);
        return $result;
    }
}
```

Regla: **no duplicar**, siempre `extends Standard` y delegar primero.

---

## 4. Theme presets

Definidos en `config/shop.php` bajo `client.html.theme-presets.default`. Variables CSS:

| Variable | Uso |
|---|---|
| `--ai-bg`, `--ai-bg-alt` | Fondos frontend |
| `--ai-primary`, `--ai-secondary`, `--ai-tertiary` (+ `-alt`) | Colores de marca |
| `--ai-danger`, `--ai-success`, `--ai-warning` | Estados |
| `--ai-radius` | Radio de bordes (sin unidad, px implicito) |
| `--bs-menu-bg`, `--bs-menu-alt-bg`, `--bs-menu`, `--bs-menu-alt` | Sidebar admin |
| `--ai-nav-bg`, `--ai-nav-text`, `--ai-nav-text-hover`, `--ai-nav-logo-height` | Navbar tienda |

Estos valores los puede sobreescribir el admin desde `Settings -> Theme` y se persisten en `mshop_locale_site.config['theme/default']`.

---

## 5. Gate `admin` para Aimeos

Definido en `app/Providers/AppServiceProvider.php:24`.

```php
Gate::define('admin', function (User $user, $class, $roles) {
    if ($user->superuser) return true;
    foreach ((array) $roles as $role) {
        if ($user->hasAimeosGroup($role)) return true;
    }
    return false;
});
```

Aimeos consulta este Gate para autorizar acceso al JQAdm y subparts. **Superuser pasa todo**.

---

## 6. Helper `User::hasAimeosGroup()`

`app/Models/User.php:58`. Hace join directo a `mshop_*`:

```php
mshop_customer (code = users.email)
  -> mshop_customer_list (domain='group')
    -> mshop_group (code = $groupCode)
```

Grupos Aimeos en uso: `admin`, `editor`, `customer`, `super` (definidos en `config/shop.php:10`).

---

## 7. Redireccion post-login por rol

`AuthenticatedSessionController::homeForUser()` (estatico, usado por TODOS los controllers de auth):

| Condicion | Destino |
|---|---|
| `superuser = 1` | `/admin` |
| `hasAimeosGroup('editor')` | `/admin` |
| resto | `/` |

Esto evita que un cliente aterrice en `/dashboard` (que ni existe para customers).

Tambien expuesto como `Route::get('/dashboard', ...)` que delega a la misma logica, para enlaces antiguos tipo `/dashboard`.

---

## 8. Ruta de perfil `/profile/me`

Originalmente Breeze publica `/profile`. Aimeos ya usa `/profile` para su panel de cuenta (ruta `account-index`), asi que choca.

Workaround: rutas en `routes/web.php:20-24` apuntan a `/profile/me`. El navbar y los emails de Breeze se han actualizado a esa URL. **No cambiar a `/profile`**.

---

## 9. Restricciones JQAdm

`config/shop.php:121-127`:

```php
'admin' => [
    'jqadm' => [
        'resource' => [
            'customer' => ['groups' => ['admin', 'super']],
            'users'    => ['groups' => ['admin', 'super']],
        ],
        ...
    ],
],
```

El rol `editor` (vendedor) **NO ve** los recursos `customer` ni `users` en el JQAdm. Esto evita que un vendedor vea/modifique la lista de clientes del marketplace.

Si se quiere restringir otro recurso, se anade aqui la entrada `recurso => ['groups' => [...]]`.

---

## 10. Overrides de plantillas Aimeos

En `resources/views/vendor/shop/`. Listado actual:

| Archivo | Override de |
|---|---|
| `base.blade.php` | Layout raiz Aimeos (inyecta drawer wishlist, navbar custom) |
| `catalog/home.blade.php` | Home del catalogo |
| `catalog/list.blade.php` | Listado de productos |
| `catalog/tree.blade.php` | Arbol de categorias |
| `account/favorites.blade.php` | Pagina "Mis favoritos" |
| `jqadm/index.blade.php` | Indice del panel admin |
| `partials/exi-wishlist-icon.blade.php` | Icono de corazon en cards |
| `partials/exi-drawer.blade.php` | Drawer lateral con favoritos |

Al añadir override nuevo: **copiar solo el archivo a tocar**, nunca todo un directorio (ver AGENTS.md §5.3).

---

## 11. Comando `exi:import-products`

`php artisan exi:import-products` importa productos desde `public/Productos de prueba/` al catalogo Aimeos.

Hace:

1. Bootstrap contexto Aimeos (`app('aimeos.context')->get(false)`).
2. Asegura categoria "Salud y Suplementos" (`code=salud`, padre de Home `id=1`).
3. Para cada producto del array `$this->products`:
   - Inserta `mshop_product`, `mshop_text` (name/short/long), `mshop_price` (COP), `mshop_stock`, `mshop_media` (con thumbnails via `Media::scale()`).
   - Enlaza via `mshop_product_list` (posicion 0).
   - Enlaza a la categoria via `mshop_catalog_list` (ambas direcciones).
4. Opcional: `--fix-bee-venom` para parchar solo el producto `BELL-003`.
5. Reconstruye el indice de busqueda llamando `aimeos:jobs index/rebuild`.

### Datos hardcoded

Los productos y rutas a imagenes viven en `buildProductData()` del propio comando. No es un importador generico; es un seed del catalogo demo. Si se borran las imagenes en `public/Productos de prueba/`, el comando las omite con warning pero continua.

---

## 12. Parciales de wishlist

- `exi-wishlist-icon.blade.php`: corazon + contador. Pensado para inyectarse en la card de producto. Usa endpoint `POST /favorites` con `fetch` JSON.
- `exi-drawer.blade.php`: drawer lateral Alpine.js con lista de favoritos actuales + enlace a `/favorites`. Se incluye una vez en `base.blade.php`.

Si se redisea la card de producto, **mantener accesible** el icono via slot o `include`.

---

## 13. Hook de reindexado

`ImportProducts.php:56` invoca `aimeos:jobs index/rebuild` al final. Esto es necesario porque Aimeos usa un indice propio (basado en mshop_index_*); un insert directo a `mshop_product` no actualiza el indice.

Cualquier otro comando que cree/modifique productos deberia llamar el mismo job al terminar.

---

## Como anadir una personalizacion nueva

1. Implementa la feature en `app/`, `ext/`, `config/` o `resources/views/vendor/shop/` segun aplique.
2. Si toca Aimeos directamente: **extension propia**, nunca `vendor/aimeos/...`.
3. Documentala aqui en este archivo (entrada nueva en la tabla + seccion).
4. Si introduce una tabla nueva: migracion Laravel + entrada en `docs/DATABASE.md`.
5. Si introduce una decision arquitectonica: nuevo ADR en `docs/adr/`.
6. Si introduce una variable `.env` nueva: entrada en AGENTS.md §13.