# TASK-019 — Gestión de archivos e integraciones (buscadores, agenda, redes, semántica)

**Categoría:** CMS
**Requisito origen:** REQ-030 (SPEC-001)
**Prioridad:** Medium *(estimado)*
**Story Points:** 5 *(estimado)*
**Labels:** backend, frontend, cms, drupal, integracion
**Estado:** Hecho

## Descripción técnica

Configurar la carga y publicación de archivos en formatos habituales (imágenes, PDF, etc.) con permisos por perfil, y las integraciones necesarias con buscadores (SEO/sitemap), la propia Agenda de Eventos, redes sociales (compartir/embeber) y el etiquetado semántico (tags) alineado con la norma UNE 178503.

## Auditoría real (2026-08-06)

Esta task llevaba "Pendiente" desde el inicio del panel, pero al auditar el estado real se encontró que había trabajo parcial ya hecho en sesiones anteriores (compartir social de REQ-027, campos de etiquetas semánticas de REQ-029) sin que esta task se hubiera actualizado. Estado real encontrado por sub-cláusula:

- **Carga de archivos por perfil**: parcialmente cubierto — formatos amplios (imágenes, audio, video, GPX/KML) ya existían, restringidos indirectamente vía permisos de nodo por rol (no hay Media Library separada, los ficheros van en campos file/image directamente sobre el contenido). **Bug real encontrado**: `field_ruta_track` (GPX/KML) tenía `file_extensions: txt` en la config exportada — mismo patrón de bug que el de audio/vídeo de REQ-027 (ajuste fijado en la config de storage, no en la de campo, así que Drupal caía al valor de fábrica). Corregido en `ensure_field()` (`scripts/provision-content-types.php`), que ahora corrige ajustes de campos ya existentes, no solo los nuevos.
- **Integración con buscadores (SEO)**: no iniciado. Decisión de diseño real: **no** se instala un módulo de sitemap en Drupal (`simple_sitemap`), porque este backend es headless — un sitemap generado por Drupal listaría rutas internas de la API (`/node/3`), no las URLs reales del portal que un buscador debería indexar. En su lugar, `scripts/generar-sitemap.js` (nuevo, en `02-26-web-front`) genera `public/sitemap.xml` **en tiempo de build** del front, a partir del contenido publicado real vía JSON:API, más `public/robots.txt` referenciándolo.
- **Integración Agenda de Eventos**: no iniciado. Añadido botón "Añadir al calendario" en el detalle de evento que genera un fichero `.ics` (iCalendar, RFC 5545) en el propio navegador — importable en Google Calendar/Outlook/Apple Calendar. No requiere nada del backend.
- **Social**: ya cubierto por `CompartirComponent` (REQ-027), confirmado presente en las 5 páginas de detalle.
- **Semántica/tags**: el vocabulario `etiquetas_semanticas` (UNE 178503, etiquetado libre) y los campos `field_*_etiquetas` ya existían (REQ-029), pero el contenido de ejemplo no tenía ningún valor, y solo aparcamiento/transporte los mostraban en el front. Corregido: términos de ejemplo creados (Accesible, Familiar, Aire libre, Patrimonio, Sostenible, Gratuito), asignados a los nodos de ejemplo de evento/playa/ruta, y mostrados ahora también en evento/playa/ruta-detalle (antes solo en aparcamiento/transporte). Además se exponen como meta keywords SEO (`SeoService`, nuevo).

## Trabajo realizado

**Backend** (`02-26-web-back`):
- `ensure_field()` corregido para reparar ajustes de campos ya existentes (mismo patrón que `ensure_field_mm()` de REQ-027).
- `field_ruta_track` corregido: acepta `gpx kml`, no `txt`.
- 3 nuevos términos de `etiquetas_semanticas` + asignación a los nodos de ejemplo de evento/playa/ruta (con corrección para instalaciones donde esos nodos ya existían).

**Frontend** (`02-26-web-front`):
- `SeoService` (nuevo): `Title`/`Meta` de Angular — título de página, meta description y meta keywords dinámicos en las 5 páginas de detalle.
- `IcalService` (nuevo): genera y descarga un `.ics` válido para un evento, sin dependencia del backend.
- `scripts/generar-sitemap.js` (nuevo) + `Dockerfile`: sitemap.xml real generado en cada build a partir del contenido publicado.
- `public/robots.txt` (nuevo).
- Bloque "Etiquetas" añadido a evento/playa/ruta-detalle (antes solo aparcamiento/transporte).

## Entregable / Evidencia

Verificado en local con Playwright contra el backend real: título de página dinámico (`"Feria de Mayo de Cádiz — Bahía de Cádiz"`), meta description y meta keywords correctos, fichero `.ics` descargado y validado (formato RFC 5545 correcto, escapado de comas), etiquetas semánticas visibles en evento/playa. `scripts/generar-sitemap.js` ejecutado contra producción real: genera 15 URLs (5 rutas estáticas + 10 fichas de contenido). Aplicado y confirmado en producción: `field_ruta_track` acepta `gpx kml`, etiquetas semánticas asignadas a los 4 nodos de ejemplo (incluida su traducción EN, campo no traducible y compartido correctamente).
