# TASK-020 — Contenido multimedia en fichas turísticas (audio, vídeo, foto 360°, valoración)

**Categoría:** CMS
**Requisito origen:** REQ-027 (checklist del pliego — "Generación, carga y gestión de tipos de contenido digital: texto, geolocalización, audio, vídeo, fotografía 360, valoraciones y compartir en RRSS")
**Prioridad:** High
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, frontend, multimedia
**Estado:** Hecho

## Descripción técnica

Texto y geolocalización ya existían en los tipos de contenido turísticos (evento, playa, ruta, transporte, aparcamiento). Faltaban 5 de las 7 subcláusulas del requisito:

- **Backend** (`02-26-web-back/scripts/provision-multimedia.php`, idempotente, se ejecuta en cada arranque vía `entrypoint.sh`): añade a los 5 tipos de contenido turísticos:
  - `field_{bundle}_audio` (file: MP3/OGG/WAV, máx. 20 MB)
  - `field_{bundle}_video` (file: MP4/WebM/MOV, máx. 200 MB) — subida de fichero propio, no embed a YouTube/Vimeo (decisión de alcance confirmada)
  - `field_{bundle}_foto360` (image, imagen panorámica equirrectangular)
  - `field_{bundle}_valoracion` (decimal 0-5) — valoración editorial introducida por el editor, no valoración de usuarios públicos (el portal no tiene cuentas de usuario públicas ni escritura vía API; eso sería un cambio arquitectónico mayor, fuera de alcance)
- **Frontend** (`02-26-web-front`):
  - `CompartirComponent` (standalone, `src/app/components/compartir/`): botón "Compartir" con Web Share API nativa y fallback a enlaces directos (WhatsApp, Facebook, X) cuando no está disponible. Presente de forma incondicional en las 5 páginas de detalle.
  - `Foto360Component` (standalone, `src/app/components/foto-360/`): visor panorámico interactivo con `@photo-sphere-viewer/core` (MIT, instalado como dependencia npm, sin CDN externo), arrastrable y con zoom/pantalla completa. Se muestra solo si la ficha tiene `foto360Url`.
  - Fix de CORS para ficheros estáticos (`sites/default/files`) en el `Dockerfile` del backend: el visor 360 carga el panorama vía `fetch()` (a diferencia de `<img>`, sí requiere CORS), necesario para que funcione con el front en un origen distinto.

## Corrección (2026-08-10, REQ-042) — audio/vídeo/valoración nunca se mostraban en el portal

Auditoría real: esta task documentaba `CompartirComponent` y `Foto360Component` como la parte de frontend, pero **nunca se implementó la visualización de audio, vídeo ni valoración** — los campos existían en el backend con datos reales (confirmado en producción: "Feria de Mayo de Cádiz" con audio y vídeo reales adjuntos, valoración 4.6), pero `content.service.ts`/`movilidad.service.ts` nunca los mapeaban al modelo, así que eran inaccesibles en el portal aunque un editor los rellenara. No se detectó hasta que REQ-042 (navegación y acceso a fotografías/vídeos) exigió comprobarlo explícitamente.

Corregido: añadidos `videoUrl`/`audioUrl`/`valoracion` a los modelos y servicios de las 5 fichas de detalle (evento/playa/ruta/transporte/aparcamiento), con reproductores `<video>`/`<audio>` HTML5 nativos (sin librería adicional) y la valoración integrada en las estadísticas de cada ficha. Verificado con navegador real contra datos reales de producción (proxy temporal, sin tocar producción): reproductor de vídeo y de audio con controles funcionando, "4.6/5" visible, sin errores de consola. Detalle completo en [TASK-027](TASK-027-navegacion-acceso-contenido-recursos.md).

## Entregable / Evidencia

- Campos de backend verificados en Drupal admin y vía JSON:API para los 5 tipos de contenido.
- Botón "Compartir" y visor 360 verificados end-to-end en producción (`https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app`) sobre la ficha "Playa de la Victoria": botón visible, imagen panorámica cargando sin errores de consola, arrastre/zoom funcionando.
- Bug real encontrado y corregido durante la verificación en producción: `file_extensions`/`max_filesize` se habían fijado a nivel de `FieldStorageConfig` en vez de `FieldConfig`, causando que Drupal aplicara el valor de fábrica (`txt`) y rechazara audio/vídeo reales. Corregido en `provision-multimedia.php` (ahora detecta y corrige también campos ya existentes con esos ajustes).
- Nota: la aparición inicial de "no veo el botón" tras el despliegue fue caché de PWA/service worker en el navegador de prueba, no un fallo real — confirmado en incógnito.
