# TASK-015 — Tipos de contenido personalizados, flujos editoriales y versionado

**Categoría:** CMS
**Requisito origen:** REQ-024 (SPEC-001)
**Prioridad:** Highest
**Story Points:** 8 *(estimado)*
**Labels:** backend, cms, drupal, content-types
**Estado:** Hecho

## Descripción técnica

Definir los tipos de contenido personalizados con campos estructurados necesarios para Agenda de Eventos, Playas y Movilidad Sostenible. Configurar flujos de trabajo editoriales sin necesidad de programar (borrador → revisión → publicado), edición contextual con vista previa en tiempo real, y versionado completo de entidades y contenido.

## Entregable / Evidencia

Tipos de contenido dados de alta + workflow editorial configurado (Content Moderation) + prueba de vista previa y de revertir a una versión anterior.

**Avance:**

- Script de aprovisionamiento idempotente: `scripts/provision-content-types.php` (ejecutar con `drush php:script`). Crea los 3 tipos de contenido y sus vocabularios de taxonomía asociados; se puede volver a ejecutar sin duplicar nada.
- **Evento** (Agenda de Eventos): descripcion, fecha inicio/fin, municipio, categoria, direccion, lat/lng, imagen, precio, enlace, accesible, organizador, etiquetas semanticas (UNE 178503).
- **Playa**: descripcion, municipio, estado del mar, bandera, ocupacion, longitud en metros, accesible, servicios (taxonomia), lat/lng, imagen, etiquetas semanticas.
- **Ruta** (Movilidad Sostenible): descripcion, tipo (ciclista/senderismo/accesible/urbana), municipios que recorre, distancia, duracion, dificultad, track GPX/KML, coordenadas de inicio/fin, imagen, etiquetas semanticas.
- `new_revision: true` en los 3 tipos (versionado de contenido activo desde el alta).
- Probado end-to-end: creacion de un nodo real de cada tipo, comprobado en `/jsonapi/node/{bundle}` y en el formulario `/node/add/{bundle}` sin errores.
- Patrones de alias Pathauto (`/eventos/...`, `/playas/...`, `/rutas/...`) para evitar el aviso de deprecacion de Pathauto y tener URLs limpias.
- Config exportada a `config/sync/` (244 ficheros) e imagen Docker reconstruida con la config al dia.

**Cerrado (2026-08-03):**

- Content Moderation configurado (`scripts/provision-flujos-editoriales.php`, idempotente, integrado en `entrypoint.sh`): workflow "editorial" (Borrador → Publicado → Archivado) aplicado a los 7 tipos de contenido (no solo los 3 originales). Permisos de transición concedidos a `editor_municipio`/`content_editor` para que puedan seguir publicando su propio contenido sin regresión.
- Probado end-to-end en local: nodo creado en Borrador (`isPublished() = false`) → transición a Publicado (`isPublished() = true`), verificado con `drush php:eval` contra la BD real.
- Revertir a una revisión anterior: probado end-to-end sobre un nodo real (Playa de la Victoria) — título editado, guardado como nueva revisión, revertido a la revisión original, título recuperado correctamente.
- **Vista previa en tiempo real**: hueco real, no resuelto. El módulo Quick Edit, que cubría esto en Drupal core, fue **eliminado del core a partir de Drupal 10.1** — ya no existe como opción "sin programación". Queda el botón de vista previa estándar (antes de guardar, no en tiempo real) y el módulo `contextual` (enlaces de edición en contexto). Resolver esto de verdad requeriría un módulo contrib de terceros, no incluido en este trabajo.
