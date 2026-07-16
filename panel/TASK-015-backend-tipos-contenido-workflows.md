# TASK-015 — Tipos de contenido personalizados, flujos editoriales y versionado

**Categoría:** CMS
**Requisito origen:** REQ-024 (SPEC-001)
**Prioridad:** Highest
**Story Points:** 8 *(estimado)*
**Labels:** backend, cms, drupal, content-types
**Estado:** En progreso

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

**Pendiente para cerrar la task:**

- Configurar Content Moderation (workflow borrador → revision → publicado) sobre los 3 tipos.
- Probar vista previa en tiempo real y revertir a una revision anterior.
