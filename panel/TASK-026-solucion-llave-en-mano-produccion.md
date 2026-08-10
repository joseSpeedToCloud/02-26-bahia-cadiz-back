# TASK-026 — Solución llave en mano: portal + sistemas de operatividad/gestión + integración

**Categoría:** Diseño/UX
**Requisito origen:** REQ-040 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Alta
**Story Points:** 3 *(estimado — es una verificación de cierre transversal, no desarrollo nuevo)*
**Labels:** backend, frontend, infra, produccion, integracion
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2, cita literal: *"Solución llave en mano: desarrollo y puesta en producción del portal web y de todos los sistemas necesarios para su operatividad/gestión e integración con otros sistemas."*

A diferencia de las tasks anteriores, este REQ no pide un componente nuevo — pide confirmar que **el conjunto** (portal + CMS + infraestructura) está realmente desarrollado, desplegado en producción, operable/gestionable, e integrado, no solo que cada pieza exista por separado. Esta task no reimplementa nada: verifica en vivo la integración real y remite a la evidencia ya registrada en el resto del panel para cada sistema concreto.

## Verificación real (2026-08-10)

**Portal + CMS en producción, ambos vivos:**

```
Frontend (https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app):
  /                 -> 200
  /tabs/eventos      -> 200
  /tabs/playas        -> 200
  /tabs/rutas          -> 200
  /tabs/mapa            -> 200
  /sitemap.xml           -> 200
  /robots.txt             -> 200

Backend (https://gcp-crs-back-02-26-bh-729653171489.europe-southwest1.run.app):
  /jsonapi              -> 200
  /jsonapi/node/evento  -> 200
  /jsonapi/node/playa  -> 200
  /openapi/jsonapi     -> 200
  /user/login         -> 200
```

**Integración real front↔back verificada con navegador real (Playwright), no solo "ambos responden 200"**: se navegó a `https://gcp-crs-front-.../playa/3f262307-570f-4ad9-8ee8-776801f6f041` (UUID real de "Playa de la Victoria" obtenido del JSON:API de producción) y se confirmó que el `<h1>` renderizado es literalmente `"Playa de la Victoria"` — es decir, el frontend desplegado está consumiendo datos reales del backend desplegado, en producción, ahora mismo. Sin errores de consola ni peticiones fallidas.

## Sistemas necesarios para operatividad/gestión — remite a evidencia ya registrada

No se reimplementa nada: cada pieza tiene su propia task con evidencia detallada. Aquí solo se confirma que el conjunto existe y está coherente:

| Sistema | Task | Estado |
|---|---|---|
| CI/CD (Cloud Build, despliegue automático en push a `main`) | [TASK-009](TASK-009-infra-cicd-github-actions.md) | Hecho |
| Autoescalado / arquitectura API-first | [TASK-004](TASK-004-infra-autoescalado-api.md) | Hecho |
| Backups diarios (infra, Cloud SQL, 30 días) | [TASK-005](TASK-005-infra-backups-diarios.md) | Hecho |
| Backups desde el propio CMS (contenido/ficheros/BD) | [TASK-022](TASK-022-backend-copias-seguridad-cms.md) | Hecho |
| Monitorización y alertas SLA | [TASK-006](TASK-006-infra-monitorizacion-sla.md) | Hecho (en código; no verificado en vivo en GCP esta sesión — `gcloud` sin sesión interactiva disponible) |
| IAM / cifrado / ENS básico / RGPD | [TASK-008](TASK-008-seguridad-iam-ens-rgpd.md) | En progreso (falta solo el informe formal de cumplimiento) |
| Administración de contenidos + roles/permisos | [TASK-014](TASK-014-backend-admin-usuarios-permisos.md) | En progreso (falta solo la prueba con usuario no técnico, no fabricable) |
| Arquitectura headless / API versionada | [TASK-011](TASK-011-backend-arquitectura-headless-api.md) | Hecho |

## Integración con otros sistemas — remite a evidencia ya registrada

| Integración | Task | Estado |
|---|---|---|
| JSON:API (contrato de datos front↔back) | [TASK-011](TASK-011-backend-arquitectura-headless-api.md) | Hecho, verificado en vivo arriba |
| Calendario externo (iCal / Google Calendar) | [TASK-019](TASK-019-backend-gestion-archivos-integraciones.md) | Hecho |
| SEO / motores de búsqueda (sitemap, meta tags) | [TASK-019](TASK-019-backend-gestion-archivos-integraciones.md) | Hecho, `sitemap.xml` verificado en producción arriba |
| Puntos de interés externos (Overpass API / OpenStreetMap) | [TASK-021](TASK-021-backend-geolocalizacion-mapas.md) | Hecho |
| Grafo de conocimiento / schema.org (interoperabilidad semántica) | [TASK-023](TASK-023-backend-grafo-conocimiento-une178503.md) | Hecho |
| Multiidioma (ES/EN) | [TASK-016](TASK-016-backend-multiidioma.md) | Hecho |
| Empaquetado como app móvil nativa (Capacitor/Android) | [TASK-F01](TASK-F01-portal-web-agenda-playas-movilidad.md) | Hecho (Android verificado; iOS pendiente, sin macOS disponible) |

## Huecos conocidos, no ocultados

- **TASK-014**: falta la prueba de usabilidad con un usuario no técnico real — no resoluble unilateralmente, pendiente del responsable del contrato.
- **TASK-008**: falta el informe formal de cumplimiento ENS — es un documento, no una implementación pendiente.
- **TASK-003** (entornos dev/preprod/prod separados) y **TASK-007** (certificación energética del datacenter) siguen genuinamente Pendiente — no forman parte del alcance literal de REQ-040 (que habla del portal y su operatividad/integración, no de entornos de despliegue separados ni del datacenter), pero se listan aquí para no dar una imagen más completa de la que hay.
- 4 huecos del pliego dejados en pausa explícita por instrucción del usuario (WMS/WFS/GeoJSON, notificaciones de Agenda, transporte en fichas de evento, calculadora de huella de carbono) — no forman parte de "todos los sistemas necesarios para la operatividad", son funcionalidades de producto, no de infraestructura/gestión.
- iOS sin verificar (sin macOS disponible) — ya documentado en TASK-F01.

## Entregable / Evidencia

Portal y CMS reales, desplegados, respondiendo en producción; integración front↔back verificada en vivo con navegador real contra las URLs de producción reales (no local, no simulado). El resto de "sistemas necesarios" (CI/CD, backups, monitorización, IAM, administración, integraciones externas) tienen evidencia propia ya registrada en sus tasks respectivas, enlazadas arriba — ninguna se ha tenido que fabricar o re-verificar desde cero porque ya existía evidencia real de sesiones anteriores de este mismo panel.

Pendiente exclusivamente de validación por el responsable del contrato.
