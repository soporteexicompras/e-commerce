# ADR-0001: Usar Aimeos 2025.10 como núcleo del marketplace

## Estado

Aceptada — 2026-07-01

## Contexto

Exicompras es un marketplace multi-vendor que necesita:

- Múltiples vendedores publicando productos de forma aislada.
- Un único carrito y checkout para el cliente, que consolida productos de varios vendedores.
- Panel de administración con gestión de catálogo, pedidos, clientes, precios, stock y medios.
- Localización en español (Colombia) y multi-moneda potencial.
- Stack mantenible por un equipo pequeño (1–3 devs senior).

Alternativas consideradas:

- **Bagisto**: Laravel nativo, multi-vendor más limitado y menos maduro.
- **WooCommerce**: PHP maduro, pero sobre WordPress; modelo de plugins menos limpio que las extensiones de Aimeos.
- **Desarrollo from-scratch**: máximo control, máximo costo, reimplementar carrito / checkout / multi-vendor.
- **Medusa / Vendure**: Node/JS, descartado por coherencia con stack Laravel.

## Decisión

Se adopta **Aimeos 2025.10** sobre Laravel 12 como núcleo del marketplace.

Razones:

1. **Multi-vendor nativo** vía Sites: cada vendedor aislado sin reimplementar tenancy.
2. **Multi-site jerárquico**: el owner ve todo, los editores solo su site.
3. **Carrito compartido cross-vendor**: Aimeos lo provee de fábrica (decisión compleja de implementar from-scratch).
4. **Extensibilidad limpia**: mecanismo `ext/` con `manifest.php` permite customizar sin tocar el core.
5. **Madurez**: Aimeos lleva más de 20 años activo y es el e-commerce PHP más completo para multi-vendor.
6. **Stack coherente**: sigue siendo PHP/Laravel, encaja con el perfil del equipo.

## Consecuencias

### Positivas

- Time-to-market reducido: carrito, checkout, panel admin y multi-site ya resueltos.
- Customizaciones en `ext/<nombre>/` aislables y versionables.
- Actualizar Aimeos es seguro siempre que se respeten las reglas de no tocar el core.

### Negativas / Riesgos

- **Acoplamiento al framework**: migrar a otra solución sería costoso.
- **Curva de aprendizaje Aimeos**: conceptos propios (Manager, JQAdm, Subpart, `mshop_*`).
- **Documentación Aimeos dispersa**: hay que consultar código fuente frecuentemente.
- **Compatibilidad**: Aimeos 2025.x requiere Laravel 11+. Saltos de versión mayor pueden requerir ajustes.

### Mitigaciones

- Toda customización va en `ext/` (ver §5.3 y §15 de AGENTS.md).
- Documentación interna del proyecto en este directorio `docs/`.
- Tests de integración sobre los flujos críticos antes de cada upgrade mayor de Aimeos o Laravel.