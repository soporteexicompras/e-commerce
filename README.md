# E-Commerce

Sistema de tienda en línea desarrollado con **Laravel 12** y **Aimeos**, orientado al mercado colombiano. Permite gestionar productos, categorías, pedidos, clientes y pagos desde un panel de administración completo.

---

## Tecnologías principales

- **PHP 8.3**
- **Laravel 12** — framework backend
- **Aimeos** — motor de e-commerce
- **MySQL** — base de datos
- **Tailwind CSS v3** — estilos
- **Alpine.js v3** — interactividad frontend
- **Vite** — bundler de assets

---

## Requisitos

- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL >= 8.0

---

## Instalación local

```bash
# Clonar el repositorio
git clone https://github.com/soporteexicompras/e-commerce.git
cd e-commerce

# Instalar dependencias PHP
composer install

# Instalar dependencias JS
npm install

# Configurar variables de entorno
cp .env.example .env
php artisan key:generate

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor de desarrollo
php artisan serve
```

---

## Variables de entorno relevantes

| Variable | Valor por defecto | Descripción |
|---|---|---|
| `APP_NAME` | E-Commerce | Nombre de la aplicación |
| `APP_LOCALE` | es | Idioma de la aplicación |
| `APP_FAKER_LOCALE` | es_CO | Locale para datos de prueba (Colombia) |
| `APP_TIMEZONE` | America/Bogota | Zona horaria |
| `DB_DATABASE` | e_commerce | Base de datos MySQL |

---

## Comandos útiles

```bash
# Ejecutar pruebas
php artisan test --compact

# Limpiar caché
php artisan optimize:clear

# Panel de administración Aimeos
php artisan aimeos:setup

# Servidor de desarrollo con Vite
composer run dev
```

---

## Acceso al panel de administración

Una vez instalado, el panel de administración de Aimeos está disponible en:

```
http://localhost:8000/admin
```

Los roles con acceso son `admin` y `editor`.

---

## Licencia

Este proyecto es software propietario. Todos los derechos reservados.
