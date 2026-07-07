# DATABASE.md — Esquema de base de datos

> Referencia de tablas, su origen (Laravel vs Aimeos), y como se relacionan.
> Critico: **las tablas `mshop_*` y `madmin_*` NUNCA se migran con Laravel**, solo con `php artisan aimeos:setup`.

---

## 1. Diagrama logico (alto nivel)

```
                    +--------------------+
                    |      MySQL 8       |
                    +---------+----------+
                              |
        +---------------------+---------------------+
        |                                           |
        v                                           v
  Esquema Laravel                              Esquema Aimeos
  (gestion via                                (gestion via
   php artisan migrate)                        aimeos:setup)
        |                                           |
        +- users                                     +- mshop_product
        +- password_reset_tokens                     +- mshop_product_list
        +- sessions                                  +- mshop_product_property
        +- cache / cache_locks                       +- mshop_text
        +- jobs / job_batches / failed_jobs          +- mshop_price
        +- favorites  (CUSTOM)                       +- mshop_media
                                                     +- mshop_stock
                                                     +- mshop_catalog
                                                     +- mshop_catalog_list
                                                     +- mshop_attribute_*
                                                     +- mshop_customer
                                                     +- mshop_customer_list
                                                     +- mshop_group
                                                     +- mshop_order_*
                                                     +- mshop_basket
                                                     +- mshop_service
                                                     +- mshop_plugin
                                                     +- mshop_coupon
                                                     +- mshop_locale / mshop_locale_site
                                                     +- mshop_index_* (busqueda)
                                                     +- mshop_type_*
                                                     +- madmin_cache
                                                     +- madmin_log
                                                     +- madmin_job
                                                     +- madmin_queue
```

Una sola BD. La division es por origen del esquema, no por servidor.

---

## 2. Tablas Laravel

Definidas en `database/migrations/0001_01_01_*` (template de Breeze) + `database/migrations/2026_07_01_142500_create_favorites_table.php` (custom).

### 2.1 `users`

| Columna | Tipo | Notas |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR | |
| email | VARCHAR UNIQUE | Matchea con `mshop_customer.code` (ver AGENTS.md §15 — Group) |
| email_verified_at | TIMESTAMP | nullable |
| password | VARCHAR | Hash bcrypt |
| remember_token | VARCHAR | nullable |
| superuser | TINYINT | 1 = acceso total Aimeos; 0 = depende de grupo |
| created_at / updated_at | TIMESTAMP | |

Cast en `app/Models/User.php`: `superuser => integer`, `password => hashed`.

### 2.2 `favorites` (CUSTOM)

`database/migrations/2026_07_01_142500_create_favorites_table.php`.

| Columna | Tipo | Notas |
|---|---|---|
| id | BIGINT PK | |
| user_id | BIGINT FK -> users.id ON DELETE CASCADE | nullable (guest) |
| session_id | VARCHAR(64) | nullable, indexado |
| product_id | VARCHAR(64) | ID Aimeos del producto (string, no FK por integridad entre sites) |
| product_code | VARCHAR(128) | snapshot del `code` Aimeos |
| name | VARCHAR(255) | snapshot del nombre |
| price | DECIMAL(12,2) | snapshot del precio |
| media_url | VARCHAR(500) | snapshot de la primera imagen |
| created_at / updated_at | TIMESTAMP | |

Indices:
- `(user_id, product_id)` — busqueda rapida del user
- `(session_id, product_id)` — busqueda rapida del guest
- `session_id` (suelto) — usado en cleanup

**Nota**: el snapshot (`product_code`, `name`, `price`, `media_url`) es deliberado. Si el producto Aimeos se borra/cambia, el favorito conserva el dato que el user vio al guardarlo.

### 2.3 Otras tablas Laravel

| Tabla | Proposito |
|---|---|
| `password_reset_tokens` | Tokens de recuperacion de password |
| `sessions` | Sesiones (driver database) |
| `cache` + `cache_locks` | Cache (driver database, alternativa a Redis) |
| `jobs` + `job_batches` + `failed_jobs` | Cola (driver database) |

---

## 3. Tablas Aimeos (clave)

Todas con prefijo `mshop_` (entidades de negocio) o `madmin_` (panel admin / cache / log).

### 3.1 Productos

#### `mshop_product`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | `1.` = default, `1.a.` = site-a, etc. |
| type | VARCHAR | normalmente `default` |
| code | VARCHAR | SKU visible |
| label | VARCHAR | nombre "interno" (puede ser i18n via mshop_text) |
| url | VARCHAR | slug para SEO |
| dataset | VARCHAR | extension marker |
| status | SMALLINT | 1 = visible, 0 = oculto, -1 = borrado |
| start / end | DATETIME | ventana de disponibilidad |
| scale | DECIMAL | factor de escala de unidades |
| boost | DECIMAL | boost en busqueda |
| rating / ratings | DECIMAL / INT | rating promedio + total votos |
| instock | INT | flag derivado |
| config | TEXT (JSON) | configuracion libre |
| mtime / ctime / editor | TIMESTAMP / VARCHAR | auditoria |

#### `mshop_product_list` (relaciones N:M)

Une productos con sus textos, precios, media, stock, categorias, atributos.

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| parentid | INT -> mshop_product.id | |
| siteid | VARCHAR | |
| domain | VARCHAR | `text`, `price`, `media`, `catalog`, `attribute`, `supplier`, `stock`, `service` |
| refid | VARCHAR | id del item relacionado |
| type | VARCHAR | subtipo dentro del dominio (`default`, `default:limit`, ...) |
| key | VARCHAR | indice natural `domain|type|refid` |
| pos | INT | orden de visualizacion |
| status | SMALLINT | |
| mtime / ctime / editor | | |

#### `mshop_text`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| domain | VARCHAR | normalmente `product` |
| type | VARCHAR | `name`, `short`, `long`, `url`, `meta-title`, `meta-desc`, ... |
| langid | VARCHAR | `es`, `en`, ... |
| label | VARCHAR | version corta para listados |
| content | TEXT | version larga / descripcion |
| status | SMALLINT | |

#### `mshop_price`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| domain | VARCHAR | `product` |
| type | VARCHAR | `default`, `default:limit`, `sale`, ... |
| currencyid | VARCHAR | `COP`, `USD`, ... |
| label | VARCHAR | opcional |
| quantity | INT | bloque de cantidad (1 = unitario, 10 = bloque de 10) |
| value | DECIMAL(12,2) | precio |
| costs | DECIMAL(12,2) | costo interno |
| rebate | DECIMAL(12,2) | descuento |
| taxrate | VARCHAR(JSON) | `{"IVA":19}` |
| status | SMALLINT | |

#### `mshop_media`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| domain | VARCHAR | `product`, `catalog`, ... |
| type | VARCHAR | `default`, `download`, ... |
| label | VARCHAR | nombre visible |
| mimetype | VARCHAR | `image/webp`, `application/pdf`, ... |
| url | VARCHAR | ruta en `public/aimeos/` (relativa al fs adapter) |
| fsname | VARCHAR | `fs-media`, `fs-theme`, etc. (ver config/shop.php `resource.fs-*`) |
| preview | VARCHAR | URL del thumbnail generado |
| langid | VARCHAR | nullable (imagenes universales) |
| status | SMALLINT | |

Thumbnails: Aimeos genera variantes (`s-image`, `m-image`, `l-image`) via `Media::scale()`. Tamaños definidos en `config/shop.php` -> `client.html.catalog.detail.imageset` y similares.

#### `mshop_stock`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| type | VARCHAR | `default` |
| prodid | INT -> mshop_product.id | |
| warehouseid | INT -> mshop_warehouse (si aplica) | |
| stocklevel | INT | unidades disponibles |
| backdate | DATE | nullable, fecha esperada de restock |
| timeframe | VARCHAR | texto libre ("3-5 dias") |
| mtime / ctime / editor | | |

---

### 3.2 Catalogos

#### `mshop_catalog`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| code | VARCHAR | slug unico |
| label | VARCHAR | nombre |
| url | VARCHAR | path SEO |
| config | TEXT (JSON) | |
| status | SMALLINT | |
| parentid (auto-ref) | INT | padre (Home = id 1) |

Jerarquia: Home (id 1) -> subcategorias -> sub-sub...

#### `mshop_catalog_list`

Une catalogos con productos (y atributos).

| Columna | Tipo | Notas |
|---|---|---|
| parentid | INT -> mshop_catalog.id | |
| siteid | VARCHAR | |
| domain | VARCHAR | `product` |
| refid | INT -> mshop_product.id | |
| type | VARCHAR | |
| pos | INT | orden manual |

---

### 3.3 Clientes, grupos, usuarios

#### `mshop_customer`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| code | VARCHAR | matchea con `users.email` |
| label | VARCHAR | nombre visible |
| status | SMALLINT | |
| ... | | direcciones, saldos, etc. en tablas auxiliares |

**Relacion App <-> Aimeos**: `users.email = mshop_customer.code`. Esto es lo que usa `User::hasAimeosGroup()` (`app/Models/User.php:58`).

#### `mshop_customer_list`

Une clientes con grupos (y otras relaciones).

| Columna | Tipo | Notas |
|---|---|---|
| parentid | INT -> mshop_customer.id | |
| domain | VARCHAR | `group` |
| refid | INT -> mshop_group.id | |
| type | VARCHAR | |
| pos | INT | |

#### `mshop_group`

| Columna | Tipo | Notas |
|---|---|---|
| id | INT PK | |
| siteid | VARCHAR | |
| code | VARCHAR | `admin`, `editor`, `customer`, `super` |
| label | VARCHAR | |

Grupos validos hoy: ver `config/shop.php:10` (`'roles' => ['admin', 'editor']`).

---

### 3.4 Ordenes y transacciones

| Tabla | Notas |
|---|---|
| `mshop_order` | Cabecera (customer, addresses, totals, status) |
| `mshop_order_base` | Version inmutable de la orden antes de pago |
| `mshop_order_base_product` | Lineas de productos |
| `mshop_order_base_service` | Servicios (envio, pago) |
| `mshop_order_base_address` | Direcciones de envio/facturacion |
| `mshop_order_base_coupon` | Cupones aplicados |
| `mshop_order_status` | Historial de cambios de status |

---

### 3.5 Administracion (`madmin_*`)

| Tabla | Notas |
|---|---|
| `madmin_cache` | Cache interna Aimeos (catalogos, configs, ...) |
| `madmin_log` | Logs del panel admin (no reemplazar `storage/logs/`) |
| `madmin_job` | Jobs de Aimeos (reindex, emails, ...) |
| `madmin_queue` | Cola interna de jobs |

---

## 4. Reglas de oro

### NO migrar `mshop_*` con Laravel

Las tablas `mshop_*` y `madmin_*` se gestionan **solo** con:

```bash
php artisan aimeos:setup                  # crea/actualiza tablas
php artisan aimeos:setup --option=jobs/admin/job/create-data   # carga datos demo
```

Si necesitas una columna nueva en `mshop_product`, **primero** mira si Aimeos core ya la provee (release notes). Si no, plantéate un Manager custom en `ext/`. **Nunca** `php artisan make:migration add_column_mshop_*`.

### Indice de busqueda

Cualquier bulk insert a `mshop_product` debe ir seguido de:

```bash
php artisan aimeos:jobs index/rebuild
```

Esto se hace en `ImportProducts.php:56`.

### Siteid en queries directas

Si por algun motivo haces `DB::table('mshop_*')->...` directo, **filtra por `siteid` manualmente**:

```php
DB::table('mshop_product')
    ->where('siteid', $context->locale()->getSiteId())
    ->...
```

Preferible siempre usar el Manager: `Aimeos\MShop::create($context, 'product')->search(...)`.

### Soft deletes

Aimeos usa `status = -1` para borrado logico, **no** soft deletes de Eloquent. En `mshop_*` el borrado es via Manager (`->delete()` actualiza status).

---

## 5. Charset y collation

Forzado en config Aimeos (`config/shop.php:62-63`):

```php
'defaultTableOptions' => [
    'charset' => config('database.connections.*.charset'),     // utf8mb4
    'collate' => config('database.connections.*.collation'),   // utf8mb4_unicode_ci
],
```

Si creas tablas Laravel nuevas: mismo charset/collation.

---

## 6. Como verificar el esquema en local

```bash
php artisan aimeos:setup --option=jobs/admin/job/create-data
# ver SQL generado:
php artisan db:show
php artisan db:table mshop_product
```

O via cliente MySQL (Workbench, TablePlus, DBeaver):

```sql
SHOW TABLES;
SHOW CREATE TABLE mshop_product;
SELECT * FROM mshop_locale_site;
```

---

## 7. Migraciones Laravel vs Aimeos

| Accion | Herramienta |
|---|---|
| Crear tabla nueva propia (`favorites`, etc.) | `php artisan make:migration` |
| Modificar tabla propia | nueva migracion Laravel |
| Crear/modificar tabla `mshop_*` o `madmin_*` | `php artisan aimeos:setup` (idempotente) |
| Seeders de tablas propias | `database/seeders/*` (Laravel) |
| Seeders de datos demo Aimeos | `php artisan aimeos:setup --option=setup/default/demo:1` |
| Importador de catalogo | `php artisan exi:import-products` (custom) |

---

## 8. Backup y restore

Ver `DEPLOYMENT.md` §10.

Resumen rapido:

```bash
mysqldump -u root -p exicompras > backup-$(date +%F).sql
cat backup.sql | mysql -u root -p exicompras
```

Importante: backup incluye **tanto** las tablas Laravel **como** las `mshop_*`/`madmin_*`. Si se restaura en otro entorno, ejecutar `php artisan aimeos:setup` para asegurar que el esquema Aimeos esta al dia (es idempotente).