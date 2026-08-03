# Espacio de documentación del proyecto

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** OPS-005
**Fecha:** 2026-08-03
**Estado:** Ver apartado 3 — hay una limitación real que no puedo resolver yo solo.

---

## 1. Por qué el espacio de documentación es Git, no Drive/Confluence

El propio contrato exige explícitamente: *"Todo el código y documentación son propiedad de la Mancomunidad; entrega en Git obligatoria"* (restricción contractual recogida en `CLAUDE.md` de este repo). Un espacio en Drive/Confluence sin control de versiones sería, de hecho, un **incumplimiento** de esa condición si se convirtiera en la fuente de verdad — la documentación real y versionada ya vive en los 4 repositorios Git del proyecto. Drive/Confluence, si se usan, deben ser una capa de consulta para perfiles no técnicos, nunca la fuente de verdad.

## 2. Mapeo real de las carpetas pedidas por el pliego

| Carpeta pedida | Ubicación real |
|---|---|
| Gestión | [`02-26-bahia-cadiz-back/gestion/`](../gestion/) (metodología, gobierno, informes de migración/pruebas) + [`02-26-bahia-cadiz-back/panel/`](../panel/) (seguimiento de tasks) |
| Infraestructura | [`02-26-infra-terraform/`](../../02-26-infra-terraform/) |
| Desarrollo — CMS | [`02-26-web-back/`](../../02-26-web-back/) |
| Desarrollo — Portal / Agenda / Playas / Movilidad | [`02-26-web-front/`](../../02-26-web-front/) (los 4 módulos viven en el mismo portal, ver `gestion/MANUAL-USO-WEB.md`) |
| ENS / RGPD | [`02-26-bahia-cadiz-back/cumplimiento/`](../cumplimiento/) |
| Entregables contractuales | Ver apartado 2.1 (nuevo, creado con este issue) |

### 2.1 Índice de entregables contractuales

No existía una carpeta única que consolidara los entregables ya cerrados. Se crea `gestion/ENTREGABLES.md` como índice — enlaza cada entregable contractual real a su evidencia, sin duplicar contenido.

## 3. Limitación real — no resuelto por este trabajo

Si el pliego exige literalmente un espacio en **Drive o Confluence** (no solo "un espacio de documentación", sino ese medio en concreto, por ejemplo para que personal no técnico de la Mancomunidad o de los ayuntamientos consulte documentación sin usar Git), **eso requiere acceso real a Google Workspace o Atlassian de la organización, que no tengo**. Es una acción sobre un servicio SaaS externo, no sobre código — necesita que alguien con esas credenciales cree el espacio y, si se quiere, enlace o sincronice los documentos de `gestion/`/`cumplimiento/` ahí.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
