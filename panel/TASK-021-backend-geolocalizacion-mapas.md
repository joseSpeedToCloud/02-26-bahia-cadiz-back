# TASK-021 — Geolocalización de todos los recursos en el mapa (incl. rutas GPX y avisos de tráfico)

**Categoría:** CMS
**Requisito origen:** REQ-028 (checklist del pliego — "Capacidad para gestionar información de geolocalización en mapas de los recursos disponibles")
**Prioridad:** Medium
**Story Points:** 3 *(estimado)*
**Labels:** backend, frontend, geolocalizacion, mapa
**Estado:** Hecho

## Descripción técnica

Auditoría real (no se dio nada por hecho de documentación previa): los 6 tipos de contenido (evento, playa, ruta, transporte, aparcamiento, aviso_trafico) ya tenían campos reales de latitud/longitud en Drupal, expuestos por JSON:API — eso ya cumplía la parte de "gestionar" (vía formularios de administración de Drupal). El hueco real estaba en el front (portal Angular/Ionic): el mapa interactivo (`mapa.page.ts`, Leaflet + OpenStreetMap) solo pintaba 4 de los 6 tipos (playa, evento, transporte, aparcamiento). Faltaban:

- **Ruta**: tenía coordenadas de inicio/fin y un campo de fichero GPX/KML (`field_ruta_track`, ya existente en el backend desde antes), pero el front solo ofrecía "Descargar track" — nunca se pintaba visualmente en ningún mapa.
- **Aviso de tráfico**: no existía ni modelo ni servicio en el front — sus coordenadas estaban en Drupal pero el portal no las consumía en absoluto.

Trabajo realizado:

1. **`GpxService`** (`src/app/services/gpx.service.ts`, nuevo): descarga y parsea ficheros GPX con el `DOMParser` nativo del navegador. Se descartó la librería `leaflet-gpx` porque es BSD-2-Clause, licencia fuera de la lista permitida por el contrato (solo GPL/MIT/Apache o comercial perpetua transferible) — un GPX es XML simple, así que parsearlo a mano evita añadir una dependencia con ese problema.
2. **Capa "Rutas" en el mapa**: si la ruta tiene GPX, se pinta el track real (polilínea); si no, se pinta un trazado aproximado inicio↔fin (línea discontinua, con aviso en el popup de que es aproximado).
3. **Página de detalle de ruta**: mapa embebido con el mismo comportamiento (track real o aproximado), además del botón de descarga que ya existía.
4. **Modelo + servicio + capa "Avisos de tráfico"** (`AvisoTrafico` en `models/content.ts`, `getAvisosTrafico()` en `content.service.ts`): nuevo, no existía nada. Icono coloreado por gravedad (baja/media/alta).
5. Fichero GPX de prueba generado localmente (patrón honesto entre las coordenadas de inicio/fin reales de "Vía Verde de la Bahía de Cádiz", etiquetado como "track de prueba, trazado aproximado" — no pretende ser un levantamiento GPS real) y adjuntado a ese nodo en producción para poder verificar el flujo completo.

## Actualización — migración de Leaflet a deck.gl (mismo día)

El hallazgo de licencia de Leaflet (BSD-2-Clause, fuera de la lista permitida por el contrato) se marcó inicialmente como "no resuelto, pendiente de decisión del equipo". El cliente pidió expresamente arreglarlo, así que se investigaron alternativas reales:

- Todas las librerías de mapas de teselas maduras (Leaflet, OpenLayers, MapLibre) son BSD — es el estándar de facto en ese nicho, no existe una alternativa MIT/Apache equivalente y madura para "mapa de teselas + marcadores + interacción".
- **`deck.gl`** (`@deck.gl/core`, `@deck.gl/layers`, `@deck.gl/geo-layers`, `@deck.gl/extensions`) sí es **MIT** en su totalidad, mantenido por la OpenJS Foundation, renderiza teselas OSM por WebGL de forma independiente (no necesita Mapbox GL ni MapLibre por debajo), y es agnóstico de framework.

Se migró `mapa.page.ts` y el mini-mapa de `ruta-detalle.page.ts` de Leaflet a deck.gl:

- `TileLayer` + `BitmapLayer` para las teselas OSM (antes `L.tileLayer`).
- `ScatterplotLayer` para todos los marcadores de punto (playas, eventos, transporte, aparcamiento, avisos), coloreados por categoría. Se descartó `TextLayer` con emoji como icono: su atlas de fuente por defecto solo cubre ASCII (códigos 32-127, ver `DEFAULT_FONT_SETTINGS.characterSet` en `font-atlas-manager.js` de `@deck.gl/layers`), así que un emoji no se dibuja sin declarar explícitamente su characterSet, y aun así la fiabilidad entre navegadores con secuencias multi-codepoint (variation selectors) no está garantizada. Un círculo de color es más robusto.
- `PathLayer` + `PathStyleExtension` (para el trazado discontinuo) para las rutas — la extensión es necesaria explícitamente: `getDashArray`/`dashJustified` no tienen efecto en un `PathLayer` normal sin `extensions: [new PathStyleExtension({dash: true})]`.
- Popup propio (deck.gl no trae uno de fábrica): un div posicionado con los píxeles de pantalla del click (`PickingInfo.x/y`), fuera de la zona de Angular (los callbacks de deck.gl se disparan desde su propio bucle de eventos nativo, hace falta `NgZone.run()` para que Angular detecte los cambios).
- `leaflet`/`@types/leaflet` desinstalados, `leaflet/dist/leaflet.css` y los assets de iconos (`src/assets/leaflet/`) eliminados.

Bug real encontrado y corregido durante la verificación: el contenedor del mini-mapa de ruta (`.ruta-mapa`) no tenía `position: relative`, así que el canvas absoluto de deck.gl escapaba a un ancestro posicionado mucho más grande en vez de quedar contenido — el mapa se salía de su caja y tapaba el botón de descarga.

## Actualización — puntos de interés cercanos en rutas, contraste de chips y marcadores de inicio/fin (mismo día)

Ajustes pedidos tras revisar capturas reales de producción:

- **Contraste de los chips del mapa**: la fila de capas se solapaba con la toolbar translúcida y usaba modo "outline" (relleno transparente) — casi invisible sobre el fondo oscuro. Se bajan por debajo de la toolbar y pasan a tener siempre relleno sólido.
- **Marcadores de inicio (verde) y fin (rojo)** en las rutas, tanto en el mapa general como en el mini-mapa de detalle — antes una línea larga no dejaba claro el sentido de la ruta.
- **GPX de prueba regenerado** con una curva real (10 puntos) en vez de puntos casi colineales — antes el track "real" se veía casi igual que la línea de respaldo aproximada.
- **Puntos de interés cercanos a cada ruta** (mini-mapa de detalle): patrón reutilizado de otro proyecto propio (UBYKLOUD, `ubykloud-front-prod/src/components/maps/RouteMap.tsx`), que consulta **Overpass API** (servicio público y gratuito de OpenStreetMap) por miradores, picos, castillos, monumentos, etc. cerca de la ruta, filtrados por distancia real a la línea (no solo por bounding box). UBYKLOUD renderiza esto con Leaflet — aquí se reutilizó la lógica de consulta/filtrado (`OverpassService`, nuevo) pero el renderizado sigue siendo deck.gl, ya que Leaflet (BSD-2-Clause) no cumple la lista de licencias permitidas por este contrato. Verificado con una llamada real a Overpass API contra el track de "Vía Verde".

## Nota de cumplimiento (auditoría contra el pliego real, 2026-08-06)

El PPT (ap. 4.4, Módulo Playas) menciona literalmente: *"Integración con servicios cartográficos estándar (OpenStreetMap, Leaflet, Google Maps o similar)."* — es decir, el propio pliego cita Leaflet como ejemplo. Esto no contradice la migración a deck.gl: la cláusula 14 del PPT (licencias de terceros) es una lista cerrada y literal — GPL/MIT/Apache o comercial perpetua transferible — sin la salvedad de "o equivalente" que sí aparece en otras cláusulas (p. ej. la de Angular/Ionic, ap. 4.2). Leaflet es BSD-2-Clause, fuera de esa lista. La mención de Leaflet en el ap. 4.4 es un ejemplo ilustrativo de herramienta cartográfica ("...o similar"), no una excepción a la cláusula de licencias; deck.gl es precisamente ese "similar" que sí cumple. Se documenta aquí explícitamente por si en una revisión de la Mancomunidad se pregunta por qué no se usó la librería citada textualmente en el pliego.

**Hueco pendiente detectado en esta misma auditoría (no cubierto por ninguna task del panel)**: el PPT ap. 4.9 (Interoperabilidad) exige que "toda integración GIS" para publicación/consumo de datos georreferenciados use **WMS/WFS/GeoJSON**. Ni el backend (JSON:API de Drupal) ni el front (`GpxService`, `OverpassService`) exponen o consumen estos estándares OGC hoy — los tracks se sirven como fichero GPX crudo y los recursos como JSON:API estándar, no GeoJSON. Pendiente de decidir si se aborda en esta task o en una nueva.

## Entregable / Evidencia

- Verificado en local con Playwright tras la migración a deck.gl: capa "Rutas" muestra track real (línea continua) para "Vía Verde" y trazado aproximado discontinuo para "Sendero litoral Rota - El Puerto de Santa María"; todos los marcadores de círculo se ven y responden al click con popup propio (título, categoría, botón "Ver ficha"); página de detalle de ruta muestra el mini-mapa embebido con el track, correctamente encuadrado y contenido. Sin errores de consola en ninguna vista. Build de producción (`ng build --configuration production`) limpio, sin errores ni problemas de presupuesto de tamaño.
- Aplicado en producción: GPX de prueba subido al bucket real y enlazado al nodo real de "Vía Verde de la Bahía de Cádiz" (`https://gcp-crs-back-02-26-bh-729653171489.europe-southwest1.run.app/sites/default/files/rutas/via-verde-track.gpx`, HTTP 200).
