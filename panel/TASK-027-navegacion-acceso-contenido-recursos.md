# TASK-027 — Navegación y acceso claro, ordenado y geoposicionado al contenido (info, fotos, vídeos)

**Categoría:** Diseño/UX
**Requisito origen:** REQ-042 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 3 *(incluye el fix real de audio/vídeo/valoración, no solo verificación)*
**Labels:** frontend, portal, multimedia
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2, cita literal: *"Navegación y acceso al contenido de los recursos (información, fotografías, vídeos) de forma clara, ordenada y geoposicionada."*

## Auditoría real (2026-08-10) — hueco encontrado, no asumido por TASK-020

Al comprobar literalmente "acceso... a vídeos", se encontró que **nunca se implementó en el frontend** — `TASK-020` (REQ-027) documentaba el backend (campos `field_*_audio`/`field_*_video`/`field_*_valoracion`, con datos reales en producción) y dos componentes de frontend (`CompartirComponent`, `Foto360Component`), pero **audio, vídeo y valoración jamás se mapearon en `content.service.ts`/`movilidad.service.ts`** — ni existía ningún `<video>`/`<audio>` en ninguna de las 5 páginas de detalle. Un editor podía subir un vídeo real desde el CMS y este quedaba permanentemente inaccesible en el portal.

Confirmado en producción antes de corregirlo: el nodo real "Feria de Mayo de Cádiz" tiene un audio y un vídeo reales adjuntos (`field_evento_audio`/`field_evento_video` con ficheros reales) y valoración `4.6` — ninguno llegaba a mostrarse.

## Corrección aplicada (frontend, `02-26-web-front`)

- `models/content.ts` (Evento/Playa/Ruta) y `models/movilidad.ts` (Transporte/Aparcamiento): añadidos `videoUrl?`, `audioUrl?`, `valoracion?`.
- `content.service.ts`/`movilidad.service.ts`: mapeo de `field_*_video`/`field_*_audio` (vía `refFileUrl()`, mismo helper ya usado para `foto360Url`) y `field_*_valoracion` (vía `toNumber()`), añadidos también a los `include` de cada petición JSON:API.
- Las 5 páginas de detalle: `<video controls preload="metadata">`/`<audio controls>` HTML5 nativos (sin librería adicional — a diferencia del visor 360, que sí necesita `@photo-sphere-viewer/core` porque un `<img>` no puede renderizar una panorámica), y la valoración integrada como una estadística más (`"4.6/5"`) junto al resto de metadatos de cada ficha.
- Estilo `.media-player` añadido una sola vez al mixin compartido `_ficha-detalle.scss`, no duplicado en las 5 páginas.

## Verificación real (2026-08-10) — con navegador real, contra datos reales de producción

Sin necesidad de tocar producción ni levantar Docker: se compiló el frontend local con el fix aplicado y se conectó temporalmente (proxy de `ng serve`, revertido después) contra el JSON:API real de producción, para probar el código nuevo con datos reales de verdad (el nodo real "Feria de Mayo de Cádiz").

- Reproductor de **vídeo** real, con controles (play, volumen, pantalla completa, 0:00/0:30) — carga el fichero real de producción.
- Reproductor de **audio** real, con controles (0:00/0:08) — carga el fichero real de producción.
- **Valoración** "4.6/5" visible junto a fecha/dirección/accesibilidad.
- Sin errores de consola.
- Cambios de entorno de prueba (proxy, `environment.ts`) revertidos tras la verificación; `git status` confirma que solo quedan los cambios reales de producto.

## Navegación clara, ordenada y geoposicionada — resto de criterios

- **Clara/ordenada**: mismo orden en las 5 fichas (imagen → compartir → foto 360 → vídeo/audio → metadatos → descripción → etiquetas), ya establecido por TASK-020 y respetado aquí.
- **Geoposicionada**: cada recurso es accesible desde el mapa interactivo unificado (`/tabs/mapa`, TASK-021/TASK-025/TASK-F02), que ya georreferencia los 6 tipos de contenido y enlaza a la ficha de detalle de cada uno — no se duplica un mini-mapa en cada ficha individual (solo la ficha de ruta lo tiene, por ser específico de mostrar el track).

## Entregable / Evidencia

Fix real de código (no solo documentación) para el hueco de audio/vídeo/valoración, verificado con navegador real contra datos reales de producción. Corrección retroactiva de TASK-020 para que no siga afirmando una cobertura de frontend que no era completa.

Pendiente exclusivamente de validación por el responsable del contrato.
