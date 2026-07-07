# Documentacion de Exicompras

Este directorio contiene documentacion extendida del proyecto.

## Indice

- [AGENTS.md](../AGENTS.md) — Contexto y directrices para el agente IA (rol, stack, convenciones, reglas Aimeos, anti-patrones).
- [`adr/`](./adr/) — Architecture Decision Records: decisiones grandes y su justificacion.
- `runbooks/` — Procedimientos operativos paso a paso (pendiente).
- `api/` — Documentacion de integraciones externas (pendiente).

### Guias tecnicas

- [`ARCHITECTURE.md`](./ARCHITECTURE.md) — Arquitectura del sistema: capas, modulos, ciclo de vida de requests, multi-site.
- [`CUSTOMIZATIONS.md`](./CUSTOMIZATIONS.md) — Inventario de codigo propio (wishlist, extension tema, gates, overrides, comandos). Punto de entrada antes de anadir features.
- [`AIMEOS-INTEGRATION.md`](./AIMEOS-INTEGRATION.md) — Como este proyecto integra Aimeos: multi-site, managers, JQAdm, templates, eventos, upgrades.
- [`DATABASE.md`](./DATABASE.md) — Esquema de BD: tablas Laravel vs `mshop_*` vs `madmin_*`, relaciones, reglas de migracion.

## Como anadir documentacion

- **ADRs nuevos**: copia [`adr/0001-use-aimeos-as-marketplace-core.md`](./adr/0001-use-aimeos-as-marketplace-core.md) como plantilla y sigue el formato Nygard (ver §16 de AGENTS.md).
- **Runbooks**: usar `runbooks/` con pasos numerados, "esperado vs observado", comandos exactos y rollback.
- **API externa**: `api/<servicio>.md` con auth, endpoints usados, rate limits, webhooks.
- **Personalizacion nueva**: anadir entrada en [`CUSTOMIZATIONS.md`](./CUSTOMIZATIONS.md).
- **Decision arquitectonica**: nuevo ADR en `docs/adr/`.
- **Mantener este indice actualizado** cada vez que se anada un documento nuevo.

## Convenciones de los `.md` aqui

- Idioma: espanol (Colombia).
- Encabezados: `#` para titulo, `##` para secciones, `###` para subsecciones.
- Codigo en bloques con lenguaje (` ```bash `, ` ```php `).
- Tablas cuando aporten densidad (stack, comparativas, mapeos).
- Sin emojis salvo que el autor los solicite explicitamente.
- Citaciones a codigo con `archivo.php:42` para navegacion directa.