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
| 14 | Categorias especiales Influencers + Coleccionistas | Comando artisan + card destacada + hero animado | ver seccion 14 abajo |
| 15 | Sidebar de categoria sin `pinned`/`last-seen` | Config Aimeos + overrides Blade | `config/shop.php`, `resources/views/vendor/shop/catalog/{tree,list}.blade.php` |
| 16 | Sidebar de filtros pulido (cosmético) | Override Aimeos ext + CSS | `ext/exicompras-theme/templates/client/html/catalog/filter/*.php`, `public/css/exicatalog.css` |
| 17 | Footer rediseado + páginas legales colombianas | Vistas Blade nativas + CSS + rutas | `resources/views/legal/`, `resources/views/vendor/shop/base.blade.php`, `public/css/exifooter.css`, `routes/web.php` |

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

## 14. Categorias especiales: Influencers y Coleccionistas

Dos verticales adicionales del catalogo:

| Categoria | Code | Nivel de tratamiento | URL |
|---|---|---|---|
| **Influencers** | `influencers` | **Especial** — card con badge animado dorado en el home + hero fullscreen con animacion de entrada + stagger reveal de productos al cargar | `/shop/influencers~{id}` |
| **Coleccionistas** | `coleccionistas` | **Regular** — se muestra como una categoria mas en el grid, sin badge ni animacion | `/shop/coleccionistas~{id}` |

### Setup (primera vez)

```bash
php artisan exi:seed-special-categories --bump-theme
```

Crea las 2 categorias (idempotente: si ya existen las deja igual), las pone como hijas de Home (`id=1`), y opcionalmente bumpea `theme_version` para forzar recarga del CSS.

Los IDs reales se imprimen en consola. `home.blade.php` los resuelve por `code` en runtime, asi que no es necesario ajustar el array hardcoded aunque cambien.

### Productos de ejemplo

```bash
php artisan exi:seed-special-products
```

Inserta 13 productos demo (idempotente: si ya existe el `code` lo salta) y reconstruye el indice de busqueda:

| Categoria | Codigos | Nichos |
|---|---|---|
| **Influencers** (6) | `INF-001` a `INF-006` | Skincare coreano, auriculares retro, camara instantanea, lentes vintage, zapatillas streetwear, difusor ceramica |
| **Coleccionistas** (7) | `COL-001` a `COL-007` | Figura edicion limitada, moneda plata 999, comic primera edicion, carta calificada PSA, muneca anos 80, postal 1920s, miniatura diecast |

Los productos **no incluyen imagenes** (no se suben archivos). Salen con el placeholder emoji del home (estrella para Influencers, trofeo para Coleccionistas) y sin preview en la pagina de detalle. Para agregar imagenes: admin entra a `/admin/default/jqadm/product`, edita el producto, tab **Media** -> sube. El script `ImportProducts` (`exi:import-products`) si maneja thumbnails automaticos; este es solo data basica para llenar el catalogo rapido.

### Asignacion de productos

**Manual desde el panel admin** (la intencion acordada). El superuser/admin:
1. Entra a `/admin/default/jqadm/product`.
2. Edita el producto.
3. En la pestana **Catalog** lo agrega a la categoria `influencers` o `coleccionistas`.

`home.blade.php` consulta el join con `mshop_product_list` (dominio `catalog`), asi que los productos aparecen automaticamente en la seccion de la categoria correspondiente y en el grid del home.

### Card Influencers en el home

`home.blade.php:227-237` agrega la clase `exicat-card--influencer` a la card y un `<span class="exicat-special-badge">INFLUENCERS</span>`. La card va PRIMERA en el array de `$categories` para que aparezca al inicio del grid.

Estilos en `exihome.css`:
- Borde dorado 1.5px, fondo con gradiente sutil hacia amarillo claro.
- Badge dorado-naranja con `animation: exi-influencer-badge-pulse 2s infinite` (escala + sombra).
- Hover: borde mas oscuro y sombra dorada.

`prefers-reduced-motion: reduce` desactiva la animacion del badge.

### Hero de Influencers en la categoria

`tree.blade.php:36-58` detecta la categoria via `request()->is('*influencers*')` y:
- Anade `exicatalog-wrap--influencers` al wrap y `exicatalog-main--influencers` al main.
- Incluye `resources/views/vendor/shop/catalog/partials/exi-influencers-hero.blade.php` antes del listado.

El hero (`exicatalog.css:1367-1453`):
- Fondo `linear-gradient(135deg, #1A1F36 0%, #2d1456 50%, #1A1F36 100%)` con dos `radial-gradient` superpuestos que se mueven lento (`exi-influencers-bg-pan 8s`).
- Badge "⭐ INFLUENCERS" pulsante.
- Titulo "Compras con **personalidad**" con la 2da palabra en gradiente dorado-naranja via `background-clip: text`.
- Contenido aparece con `exi-influencers-reveal` (fade + slide-up 30px) en 0.8s.

### Stagger reveal de productos

`exicatalog.css:1455-1478`: cada producto del listado en Influencers arranca `opacity: 0; transform: translateY(20px)` y se anima con delay incremental via `nth-child` (`.30s, .36s, .42s, ...`). El primer producto sale 0.3s despues del hero para que se note la cascada.

### Inclusiones para mantener consistencia

- `home.blade.php` resuelve los IDs reales por `code` (no hardcoded) — si el seed los asigna diferente a 7/8, funciona igual.
- Las nuevas categorias entran en el `array $emojis` del home (estrellas para Influencers, trofeo para Coleccionistas) para que los productos tengan icono apropiado en "Ofertas destacadas".
- Si en el futuro se agregan mas sub-categorias dentro de Influencers/Coleccionistas, el hero se mantendra porque la deteccion es por `f_name=influencers` o `*influencers*` en la URL, no por ID.

### Cache-bust

`exicatalog.css` carga con `?v={theme_version}`. Al seedear con `--bump-theme` se incrementa, forzando la recarga. Si no, ejecutar `php artisan aimeos:clear` despues del seed.

---

## 15. Sidebar de categoria sin `pinned` / `last-seen`

Aimeos incluye por defecto en las paginas de categoria (`catalog-list`, `catalog-tree`) dos bloques del cliente `catalog/session`:

- `pinned` — productos que el visitante marca con el boton-pin para comparar (Productos marcados).
- `seen` — historial de productos visitados en la sesion (Ultima/s vista/s).

En estado limpio (visitante nuevo, sin pinear ni visitar nada) ambos renderizan como contenedores vacios con la cabecera visible y `(0)`. Son ruido permanente en el sidebar, sobre todo en movil donde la pantalla se llena antes de llegar al listado.

Se mantienen en la pagina de detalle (`catalog-detail`) y en la cuenta (`account-index`) por si en el futuro se quieren exponer en otra ubicacion (debajo del listado, home, etc).

### Cambios

| Archivo | Cambio |
|---|---|
| `config/shop.php` lineas 38 y 42 | Quitar `'catalog/session'` de los arrays `catalog-list` y `catalog-tree`. |
| `resources/views/vendor/shop/catalog/tree.blade.php` | Quitar `<?= $aiheader['catalog/session'] ?? '' ?>` del bloque `aimeos_header` y `<?= $aibody['catalog/session'] ?? '' ?>` del aside. |
| `resources/views/vendor/shop/catalog/list.blade.php` | Idem para `catalog-list`. |

`catalog-session` (pagina dedicada `/shop/session`) sigue activa para quien quiera enlazarla directamente.

---

## 16. Sidebar de filtros — Rediseño completo (Nivel C)

El sidebar de filtros de las paginas de categoria (`catalog-tree` / `catalog-list`) sufria varios problemas:

1. **Bloque PROVEEDORES sin valor real** — input "Buscar proveedor" sin autocompletar y un checkbox suelto, sin valor con 1-2 vendors reales en el marketplace.
2. **Bloque FILTRO apareciendo vacio** — el override CSS `:has()` no era fiable cross-browser; el wrapper `<div class="catalog-filter-attribute">` se renderizaba con un `<div class="fieldsets">` sin fieldsets.
3. **Slider con apariencia nativa** — track azul + thumb cuadrado, sin formato de moneda, sin coherencia con el resto del diseno.
4. **Headers en uppercase agresivo** y divisores duros.
5. **Sin forma de colapsar secciones** en desktop.

### Resultado final

El sidebar queda con **una sola seccion visible por defecto: PRECIO**.

- PROVEEDORES se quita completamente del sidebar de categoria (queda disponible via `/shop/session` o reintroducible cuando haya 3+ vendors reales).
- FILTRO aparece **solo si** los productos tienen atributos reales con opciones (chequeo PHP robusto en `attribute-body.php`).
- Precio trae presets de rango rapido (Menos de $100k, $100k–$300k, $300k–$600k, Mas de $600k), slider custom con track plano y thumb circular blanco/naranja, formato COP via `Intl.NumberFormat('es-CO')`.
- Ambos bloques pueden colapsarse en desktop con click en el header. El estado se persiste en `localStorage` con clave `exi_filter_collapsed`.

### Archivos

| Archivo | Cambio |
|---|---|
| `config/shop.php` (lineas 38 y 42) | Quitar `'catalog/supplier'` de `catalog-list` y `catalog-tree`. Mantiene `catalog-detail` y `account-index` para reintroduccion futura. |
| `ext/exicompras-theme/templates/client/html/catalog/filter/body.php` | Quitar `catalog/filter/search` (drawer + `else`) y `catalog/filter/supplier` (drawer + `else`). Anadir mini-JS (~30 lineas) que aplica colapsable a `.exi-section-header` y `.header-name` con persistencia en `localStorage`. |
| `ext/exicompras-theme/templates/client/html/catalog/filter/price-body.php` | Anadir 4 presets de rango (pills `<a>` clickeables que generan URL con `f_price=[min,max]`). El slider incluye `--exi-slider-fill` calculado por JS para el track progresivo, format readout con `Intl.NumberFormat`. |
| `ext/exicompras-theme/templates/client/html/catalog/filter/attribute-body.php` | **NUEVO override** que renderiza `<div class="exi-attr">` con **chips** (`.exi-chip` con icono + label) en vez de checkboxes. Early exit si `$attrMap` no tiene opciones reales. Header collapsable con chevron rotativo. |
| `public/css/exicatalog.css` | Bloque "FILTROS — Rediseño completo (Nivel C)" con card cohesiva, seccion header collapsable, slider custom (track plano naranja/gris), presets como pills, chips de atributos. ~150 lineas. |

### Detalles de UX

- **Collapsable en desktop, no en drawer.** El JS solo actua sobre `.exicatalog-aside .exi-section-header, .header-name` (afuera del drawer). En movil, el drawer siempre muestra todo el contenido.
- **Persistencia por texto del header.** `mem[key]` usa `head.textContent.trim()` como clave. Si renombras "Precio" se resetea.
- **Presets deshabilitados por rango.** Si el `priceHigh` del catalogo es menor que el `max` del preset, el pill aparece con `exi-price-preset--disabled` y `aria-disabled=true` para evitar enlaces rotos.
- **Chips con icono o solo label.** Si el atributo tiene media-icon (`getRefItems('media', 'icon', 'default')`) se pinta el icono a la izquierda; si no, solo label.

### Si se quiere reintroducir PROVEEDORES en el futuro

1. Restaurar `'catalog/supplier'` en los arrays `catalog-list` y `catalog-tree` de `config/shop.php`.
2. Anadir `<?= $this->block()->get( 'catalog/filter/supplier' ) ?>` en los dos puntos del drawer + `else` en `body.php`.
3. Opcionalmente, convertir el filtro de proveedores en chips siguiendo el patron de `attribute-body.php`.

### Si se quiere revertir todo

`git revert` de:
- `config/shop.php`
- `ext/exicompras-theme/templates/client/html/catalog/filter/body.php`
- `ext/exicompras-theme/templates/client/html/catalog/filter/price-body.php`
- `ext/exicompras-theme/templates/client/html/catalog/filter/attribute-body.php`
- Bloque CSS "FILTROS — Rediseño completo (Nivel C)" en `public/css/exicatalog.css`.

---

## 17. Footer rediseado + Paginas Legales Colombianas

El footer por defecto de Aimeos estaba en ingles, mezclaba idiomas y no cumplia con los requisitos legales de un marketplace colombiano (Ley 1480/2011 Estatuto del Consumidor, Ley 1581/2012 Habeas Data, Decreto 735/2013 Libro de Reclamaciones). Se ha rediseado completamente y se ha construido un set de paginas legales reales con contenido verificable.

### Footer

Reemplazado por un layout de 5 columnas en espanol, dark mode, con identidad de marca y seccion legal al pie.

| Columna | Contenido |
|---|---|
| **Marca** | Logo, tagline, datos de contacto (direccion, email, telefono, NIT) y 5 redes sociales (Facebook, Instagram, TikTok, YouTube, X). Las redes apuntan a `#` por ahora. |
| **Comprar** | Catalogo, Ofertas, Nuevos, Favoritos. |
| **Atencion** | Centro de ayuda, Libro de reclamaciones, PQR, Seguimiento de envio. |
| **Legal** | Terminos y Condiciones, Politica de Privacidad, Cancelaciones y retracto, Envios, Garantia, Libro de Reclamaciones. Todos linkeados a paginas reales. |
| **Empresa** | Sobre Exicompras, Contacto, Vende en Exicompras, y chips de medios de pago (Visa, Mastercard, Amex, PSE, Nequi, Daviplata). |

**Barra inferior** con copyright, cita literal de las leyes que cumple (Ley 1480/2011, Ley 1581/2012, Decreto 735/2013) y enlaces rapidos a Terminos / Privacidad / Garantia / Contacto.

### Paginas legales reales (no son CMS estatico)

8 vistas Blade nativas en `resources/views/legal/` que extienden `shop::base` (mismo navbar + footer del resto del sitio) y aplican el layout `.exicom-legal` con hero gradient, breadcrumbs, TOC lateral numerado y cuerpo con tipografia premium.

| Ruta | Vista | Contenido principal |
|---|---|---|
| `/terminos` | `legal.terminos` | 16 secciones: identificacion, aceptacion, objeto, registro, compras, precios, pagos, envios, retracto (Art. 47 Ley 1480), garantia (Art. 11), PQR (Art. 50), propiedad intelectual, limitacion, suspension, jurisdiccion, cambios. |
| `/privacidad` | `legal.privacidad` | 13 secciones: responsable, definiciones, datos recopilados, finalidad, consentimiento, terceros, transferencia internacional, seguridad, derechos del titular (ARCO + revocacion), menores, cookies, cambios. Marco: Ley 1581/2012 + Decreto 1377/2013 + Ley 1266/2008. |
| `/cancelaciones` | `legal.cancelaciones` | Antes de comprar, cancelacion pre-envio, retracto (5 dias habiles Art. 47), devolucion por defecto, estado del producto, tabla de plazos, reembolso, excepciones (Art. 46), garantia, procedimiento. |
| `/envios` | `legal.envios` | Cobertura, costos (envio gratis sobre $150.000), tabla de plazos por destino, seguimiento, novedades, restricciones, devoluciones. |
| `/garantias` | `legal.garantias` | Alcance, terminos minimos (90 dias durables), cobertura, exclusiones, plazos, procedimiento, soluciones (reparar / reponer / cambiar / devolver), costos, sanciones SIC. |
| `/reclamaciones` | `legal.reclamaciones` | Marco (Decreto 735/2013), tipos de PQR, **formulario virtual real** con campos nombre, documento, email, telefono, tipo, pedido, producto, descripcion y check de privacidad. Feedback inline. |
| `/contacto` | `legal.contacto` | Cards con 6 canales (atencion, pedidos, retracto, garantia, datos personales, legal), horarios, datos de la empresa, **formulario de contacto real**. |
| `/sobre-nosotros` | `legal.sobre-nosotros` | Historia, mision, vision, valores, modelo marketplace, compromisos legales, tabla de datos de la compania. |

Las paginas son **estaticas y versionadas en Git** (no son paginas CMS de Aimeos). Esto permite revision legal facil, traduccion futura y control absoluto del contenido.

### Datos de la empresa (placeholders documentados)

Los campos de identidad (NIT 900.000.000-0, direccion, telefono, representante legal, fecha de constitucion) son **placeholders** que se deben reemplazar con datos reales una vez se haga la matricula mercantil. Cada uno esta anotado en `sobre-nosotros.blade.php` con `(Se actualizara en el registro mercantil)` para que sea evidente que hay que completarlos.

### Correos electronicos especializados (placeholders)

| Email | Uso |
|---|---|
| `atencion@exicompras.com` | atencion general |
| `pedidos@exicompras.com` | gestion de pedidos |
| `retracto@exicompras.com` | retractos y devoluciones |
| `garantia@exicompras.com` | garantia legal |
| `datospersonales@exicompras.com` | habeas data (Ley 1581/2012) |
| `legal@exicompras.com` | terminos, contratacion vendedores |
| `envios@exicompras.com` | logistica |
| `vendedores@exicompras.com` | alta de vendedores |

Pendiente reemplazar por direcciones reales cuando se configure el correo corporativo.

### Archivos creados / modificados

| Archivo | Cambio |
|---|---|
| `resources/views/legal/{terminos,privacidad,cancelaciones,envios,garantias,reclamaciones,contacto,sobre-nosotros}.blade.php` | 8 vistas Blade con `@extends('shop::base')`, `@section('aimeos_header')` con `exifooter.css`, `@section('aimeos_body')` con el layout completo. |
| `resources/views/vendor/shop/base.blade.php` | `<footer class="exicom-footer">` con 5 columnas + barra inferior; `<link>` a `exifooter.css?v=1`. |
| `public/css/exifooter.css` | ~360 lineas con footer dark, hero gradient, TOC, cards de contacto, formulario legal, tablas, breadcrumbs. |
| `routes/web.php` | Bloque `Route::middleware(['web'])->name('legal.')->group(...)` con `Route::view(...)` para cada una de las 8 paginas. |

### Decisiones de diseno

- **Vista estatica, no CMS Aimeos.** Las paginas legales son Blade nativo, no `cms/page`. Razon: contenido controlado, revision facil via Git, sin dependencia del panel admin para existir.
- **Layout en `resources/views/legal/`** (no en `resources/views/vendor/shop/`) porque son paginas del proyecto, no overrides de Aimeos. Esto evita que cualquier actualizacion del paquete las pise.
- **Reutiliza `shop::base`.** Asi el navbar, drawer de favoritos, footer y todos los estilos del sitio ya estan cargados. Solo se anade `exifooter.css` por encima.
- **Formularios sin backend (onsubmit = `e.preventDefault()` + feedback inline).** El backend para `/reclamaciones` y `/contacto` se implementara despues (cuando exista la cola de emails). Los placeholders muestran el feedback inmediato para confirmar visualmente que el cliente entendio el formulario.
- **Redes sociales apuntando a `#`.** Documentadas en este archivo; se reemplazaran cuando se abran las cuentas.
- **Sin emojis en cabecera.** Solo en `<title>` de algunos `<h2>` para senalar icono de seccion (estilo editorial moderno). Logos de redes en SVG puro (sin dependencia externa).

### Si se quiere revertir

`git revert` de:
- `routes/web.php` (bloque `legal.*`)
- `resources/views/vendor/shop/base.blade.php` (solo el `<footer>` y el `<link>` de exifooter)
- `public/css/exifooter.css`
- Eliminar `resources/views/legal/`

Las paginas se vuelven inaccesibles y el footer vuelve al de Aimeos.

## Como anadir una personalizacion nueva

1. Implementa la feature en `app/`, `ext/`, `config/` o `resources/views/vendor/shop/` segun aplique.
2. Si toca Aimeos directamente: **extension propia**, nunca `vendor/aimeos/...`.
3. Documentala aqui en este archivo (entrada nueva en la tabla + seccion).
4. Si introduce una tabla nueva: migracion Laravel + entrada en `docs/DATABASE.md`.
5. Si introduce una decision arquitectonica: nuevo ADR en `docs/adr/`.
6. Si introduce una variable `.env` nueva: entrada en AGENTS.md §13.