# AIMEOS-INTEGRATION.md — Como este proyecto integra Aimeos

> Detalle de los puntos de contacto entre el codigo Laravel y Aimeos.
> Complementa AGENTS.md §5.3 y §15. Asume familiaridad con los conceptos basicos (Manager, JQAdm, Subpart, mshop_*).

---

## 1. Multi-site

Activado: `SHOP_MULTISHOP=true` en `.env` (mapea a `config/shop.php` `multishop`).

### siteid en tablas

Todas las tablas `mshop_*` tienen columna `siteid`. Formato: `<padre>.<hijo>.` terminado en punto.

| siteid | Site |
|---|---|
| `1.` | `default` (owner / superuser) |
| `1.a.` | sub-site `site-a` (vendedor) |
| `1.a.1.` | sub-site anidado bajo `site-a` (no usado hoy) |

### Aislamiento por Managers

Cuando se usa `Aimeos\MShop::create($context, 'product')`, el Manager anade automaticamente `siteid` al WHERE segun el site del contexto.

**Regla**: para queries a `mshop_*` siempre preferir el Manager. Si por algun motivo se hace `DB::table('mshop_*')` directo, **filtrar por `siteid` manualmente**.

### Site del contexto

`$context->locale()->getSiteItem()` devuelve el site actual. Los managers derivados de ese contexto solo veran datos de ese site (o descendientes, segun permisos).

---

## 2. Contexto Aimeos

El contexto es el objeto que carga configuracion, locale, site, usuario, cache, logger, etc. Es el "request scope" de Aimeos.

### Obtencion

```php
$context = app('aimeos.context')->get(false);   // false = no usar cache de user
```

Pasar `false` cuando se opera desde artisan o desde un punto donde el usuario no esta todavia en sesion. Pasar `true` en requests web normales.

### Locale

```php
$localeManager = \Aimeos\MShop::create($context, 'locale');
$localeItem    = $localeManager->bootstrap('default', 'es', 'COP', false);
$context->setLocale($localeItem);
```

Esto fija site=default, idioma=es, moneda=COP. Usado en `ImportProducts.php`.

### Filesystem

```php
$fs = $context->fs('fs-media');    // adaptador para public/aimeos/
$fs->write($path, $contents);
$fs->has($path);
$fs->mkdir($path);
```

---

## 3. Managers en uso

Managers con los que interactua el codigo custom:

| Manager | Uso en el proyecto |
|---|---|
| `MShop::create($ctx, 'product')` | Lectura/escritura de productos (panel admin, ImportProducts) |
| `MShop::create($ctx, 'media')` | Creacion de media con thumbnails (`scale()`) |
| `MShop::create($ctx, 'catalog')` | Gestion de categorias (ImportProducts, admin) |
| `MShop::create($ctx, 'locale')` | Bootstrap de locale (ImportProducts) |
| `MShop::create($ctx, 'customer')` | Indirectamente via `User::hasAimeosGroup()` (lee `mshop_customer`) |

Los managers de `order`, `basket`, `service`, `plugin`, `attribute`, etc. son usados internamente por Aimeos (Controllers de shop, JQAdm) sin intervencion directa del codigo custom.

### Managers custom

Hoy **no hay** managers custom. Si se necesita uno (ej. logica de negocio especifica sobre productos):

```php
namespace Aimeos\MShop\Product\Manager;

class MyCustom extends Standard
{
    // override save(), search(), etc.
}
```

Registrarlo en `config/shop.php` -> `mshop.product.manager.name = 'MyCustom'`.

---

## 4. JQAdm — el panel admin

URL: `/admin/{site}/jqadm/{resource}/{subpart}`.

Ejemplos:
- `/admin/default/jqadm/dashboard`
- `/admin/default/jqadm/product` (lista de productos)
- `/admin/default/jqadm/product/text` (textos de un producto)
- `/admin/default/jqadm/settings/theme` (nuestra subpart override)

### Recursos JQAdm disponibles

Todos los de Aimeos core: `product`, `catalog`, `customer`, `order`, `service`, `plugin`, `coupon`, `attribute`, `supplier`, `stock`, `locale`, `log`, `type`, `media`, `price`, `text`, `group`, `user`.

### Restricciones por rol

Configuradas en `config/shop.php` -> `admin.jqadm.resource.<recurso>.groups`. Hoy:

```php
'customer' => ['groups' => ['admin', 'super']],
'users'    => ['groups' => ['admin', 'super']],
```

El rol `editor` no ve `customer` ni `users`. Para restringir otro recurso, anadir entrada con los grupos permitidos.

### Subparts custom

| Recurso | Subpart | Override | Proposito |
|---|---|---|---|
| `settings` | `theme` | `Aimeos\Admin\JQAdm\Settings\Theme\Exicompras` (en `ext/`) | Bump de `theme_version` al guardar |

Para anadir un subpart nuevo a un recurso existente:

1. Crear `ext/<extension>/src/Admin/JQAdm/<Resource>/<Subpart>/Standard.php` (o nombre propio).
2. Registrar en `manifest.php` -> `admin` si lleva templates.
3. Configurar en `config/shop.php` -> `admin.jqadm.<resource>.subparts.<subpart> = true`.

---

## 5. Plantillas / templates

### Orden de resolucion

De mayor a menor prioridad:

1. `ext/<extension>/templates/`
2. `resources/views/vendor/shop/`
3. `vendor/aimeos/.../templates/`

### Que esta overridden hoy

Ver `docs/CUSTOMIZATIONS.md` §2 (extension tema) y §10 (overrides Blade).

### Como añadir un override

```bash
# 1. Localizar el archivo original
vendor/aimeos/aimeos-laravel/src/views/catalog/list.blade.php

# 2. Copiar SOLO ese archivo (no todo el directorio) a:
resources/views/vendor/shop/catalog/list.blade.php

# 3. Modificar. Limpiar caches:
php artisan view:clear && php artisan cache:clear
```

**Nunca** modificar el archivo en `vendor/`. **Nunca** copiar todo un directorio.

### Theme caches

Aimeos cachea las plantillas en `madmin_cache`. Despues de modificar:

```bash
php artisan aimeos:cache  # warmup
# o
rm -rf storage/aimeos/cache/*  # limpiar
```

Nuestra subpart `Theme\Exicompras` bumpea `theme_version` en cada save, lo que tambien rompe cache del cliente via query string de Vite/asset.

---

## 6. Busqueda y filtros

### Indice

Aimeos usa su propio indice (`mshop_index_*` / `mshop_index_text_*` segun version). Las inserciones directas a `mshop_product` NO actualizan el indice.

**Workaround**: ejecutar `php artisan aimeos:jobs index/rebuild` despues de cualquier bulk insert (lo hace `ImportProducts.php:56`).

### Filtros del frontend

En `config/shop.php` -> `page.catalog-list` y `page.catalog-tree` se listan los componentes. Hoy:

- `catalog/filter` — filtros laterales (override por extension tema)
- `catalog/price` — slider de precio
- `catalog/supplier` — filtro por vendedor
- `catalog/attribute` — filtro por atributos (color, talla, etc.)

---

## 7. Hooks / eventos utiles

| Evento | Cuando se dispara | Caso de uso |
|---|---|---|
| `order.checkout.buy` | Compra confirmada | Email de confirmacion, metricas |
| `order.checkout.fail` | Pago fallido | Notificar al customer, reintento |
| `customer.created` | Registro de cliente | Asignar grupo inicial |

Hoy **no hay listeners propios** registrados. Para anadir uno: `app/Listeners/*` + `EventServiceProvider::$listen`.

---

## 8. Permisos: Gate `admin`

Aimeos consulta `$gate->check('admin', $class, $roles)` para autorizar. Definido en `AppServiceProvider::boot()`.

```php
Gate::define('admin', function (User $user, $class, $roles) {
    if ($user->superuser) return true;
    foreach ((array) $roles as $role) {
        if ($user->hasAimeosGroup($role)) return true;
    }
    return false;
});
```

- `$roles` es el array configurado en `admin.jqadm.resource.<recurso>.groups`.
- Si el Gate retorna `false`, Aimeos devuelve 403/redirect al login.
- Si el Gate retorna `true`, Aimeos sigue con la logica del subpart.

**Regla**: cualquier modificacion a la logica de autorizacion debe pasar por este Gate, no por checks dispersos en controllers.

---

## 9. Datos sensibles / ciclo de vida

| Dato | Origen | Persistencia |
|---|---|---|
| Password | Breeze | `users.password` (Hash) |
| Sesion | Laravel | `sessions` (driver configurable) |
| Carrito activo | Aimeos | `mshop_basket` (TTL via config) |
| Favoritos | Custom | `favorites` (Laravel) |
| Ordenes | Aimeos | `mshop_order` + `mshop_order_*` |
| Cache de catalogo | Aimeos | `madmin_cache` |
| Logs | Aimeos + Laravel | `madmin_log`, `storage/logs/laravel.log` |

---

## 10. Antes de un upgrade de Aimeos

Checklist:

1. Revisar release notes de Aimeos 2025.x.
2. Confirmar que `php` >= 8.4.1 (segun DEPLOYMENT.md §12.1).
3. Backup BD + `vendor/`.
4. `composer update aimeos/aimeos-laravel aimeos/aimeos-core aimeos/aimeos-base --with-all-dependencies`.
5. `php artisan aimeos:setup` (puede anadir tablas/columnas nuevas).
6. Probar manualmente: home, catalogo, detalle, carrito, checkout, login admin, creacion de producto, edicion de tema.
7. Re-ejecutar tests E2E.
8. Limpiar `madmin_cache`.

---

## 11. Recursos donde profundizar

| Tema | Donde mirar |
|---|---|
| Config completa | `config/shop.php` (comentada por secciones) |
| Lista de managers | `vendor/aimeos/aimeos-core/src/MShop/*/Manager/Standard.php` |
| JQAdm base | `vendor/aimeos/ai-admin-jqadm/src/Admin/JQAdm/*` |
| Templates base | `vendor/aimeos/aimeos-laravel/src/views/` y `vendor/aimeos/ai-client-html/templates/client/html/` |
| Doc oficial | https://aimeos.org/docs/2025.x/ |