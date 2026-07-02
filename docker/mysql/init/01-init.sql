-- ════════════════════════════════════════════════════════════════════════════
--  Exicompras — Inicialización de MySQL
--  Se ejecuta automáticamente la primera vez que se levanta el container `db`
--  (gracias a /docker-entrypoint-initdb.d/ en la imagen oficial mysql:8.0).
-- ════════════════════════════════════════════════════════════════════════════

-- Crear DB con charset y collation correctos para Aimeos/Laravel
CREATE DATABASE IF NOT EXISTS `exicompras`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Aimeos crea sus propias tablas con migraciones, pero dejamos charset
-- correcto desde el inicio para evitar conversiones costosas.

-- Otorgar privilegios al usuario de la app (ya se crea vía env vars,
-- pero aseguramos que tenga todos los permisos sobre la DB).
GRANT ALL PRIVILEGES ON `exicompras`.* TO 'exicompras'@'%';
FLUSH PRIVILEGES;
