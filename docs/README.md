# Documentación de Exicompras

Este directorio contiene documentación extendida del proyecto.

## Índice

- [AGENTS.md](../AGENTS.md) — Contexto y directrices para el agente IA (rol, stack, convenciones, reglas Aimeos, anti-patrones).
- [`adr/`](./adr/) — Architecture Decision Records: decisiones grandes y su justificación.
- `runbooks/` — Procedimientos operativos paso a paso (pendiente).
- `api/` — Documentación de integraciones externas (pendiente).

## Cómo añadir documentación

- **ADRs nuevos**: copia [`adr/0001-use-aimeos-as-marketplace-core.md`](./adr/0001-use-aimeos-as-marketplace-core.md) como plantilla y sigue el formato Nygard (ver §16 de AGENTS.md).
- **Runbooks**: usar `runbooks/` con pasos numerados, "esperado vs observado", comandos exactos y rollback.
- **API externa**: `api/<servicio>.md` con auth, endpoints usados, rate limits, webhooks.
- **Mantener este índice actualizado** cada vez que se añada un documento nuevo.

## Convenciones de los `.md` aquí

- Idioma: español (Colombia).
- Encabezados: `#` para título, `##` para secciones, `###` para subsecciones.
- Código en bloques con lenguaje (` ```bash `, ` ```php `).
- Tablas cuando aporten densidad (stack, comparativas, mapeos).
- Sin emojis salvo que el autor los solicite explícitamente.