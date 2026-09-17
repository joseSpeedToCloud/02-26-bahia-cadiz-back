# TASK-039 — Búsqueda y filtrado de eventos (REQ-070)

**Categoría:** Frontend
**Requisito origen:** REQ-070 (issue #91 / Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.3)
**Prioridad:** Media
**Story Points:** 8
**Labels:** frontend, eventos, agenda, mapa, geolocalizacion
**Estado:** Hecho (con matiz — ver "Fuera de alcance")

## Descripción técnica

Búsqueda y filtrado: rango de fechas, categoría del evento (multiselección), municipio/localidad, geolocalización (manual o automática); resultados en listado ordenable y sobre mapa interactivo.

## Auditoría inicial (antes de esta task)

Verificado el código completo de `eventos.page.ts`/`.html` (frontend, `02-26-web-front`):

| Sub-requisito | Estado previo |
|---|---|
| Rango de fechas | No existía |
| Categoría multiselección | Solo selección única (con expansión jerárquica padre→hijos) |
| Municipio/localidad | Sin selector; solo coincidía por casualidad en el buscador de texto libre |
| Geolocalización | No existía en Eventos; existía (solo automática) en la página de Mapa general, transversal a todas las capas |
| Listado ordenable | Orden fijo por fecha, fijado en el backend (`sort=field_evento_fecha_inicio`) |
| Resultados sobre mapa interactivo | Los eventos aparecían en el Mapa general (página separada, sin los mismos filtros) |

## Decisiones de alcance (usuario)

- Construir ahora: rango de fechas, categoría multiselección, filtro de municipio, listado ordenable.
- Geolocalización: solo automática (reutilizando el patrón "Cerca de mí" ya existente en `mapa.page.ts`), dentro de la propia página de Eventos. La entrada manual de una dirección se deja fuera de alcance por requerir un proveedor de geocodificación nuevo (dependencia externa con coste/límites a decidir aparte).
- Mapa interactivo: se construye un mapa embebido en la propia página de Eventos (no solo enlazar al Mapa general), porque el requisito pide explícitamente que los **resultados** (ya filtrados) se vean también en mapa, no un mapa genérico con otro contenido.

## Implementado (`02-26-web-front/src/app/pages/eventos/`)

- **`eventos.page.ts`**: `categoriasSeleccionadas` pasa de `string|null` a `Set<string>` (multiselección real); `municipioSeleccionado` (derivado de los municipios presentes en los eventos ya cargados, sin servicio nuevo); `fechaDesde`/`fechaHasta` con filtrado por `fechaInicio`; `orden: 'fecha-asc'|'fecha-desc'|'alfabetico'|'cercania'`; geolocalización automática (`localizarme()`, `toggleCercania()`, cálculo de distancia Haversine) portada del mismo patrón de `mapa.page.ts`; mapa embebido con deck.gl (misma librería ya usada en el proyecto, sin nueva dependencia) mostrando solo `this.eventos` (el resultado ya filtrado), con popup y navegación a ficha igual que en el Mapa general.
- **`eventos.page.html`**: chips de categoría con estado multiselección visual; chips de municipio; inputs de fecha desde/hasta; `ion-select` de orden (la opción "Cercanía" solo aparece si ya se resolvió la ubicación); `ion-segment` Lista/Mapa; contenedor del mapa + popup + botón de localizarme, reutilizando el marcado de `mapa.page.html`.
- **`eventos.page.scss`**: estilos nuevos para filtros avanzados y el mapa embebido, adaptados de `mapa.page.scss`.

## Verificación real (no solo compilación)

`ng build --configuration production`: sin errores. Además, probado interactivamente con Playwright contra el backend real de producción (apuntando `environment.ts` temporalmente, revertido después — cambio nunca commiteado):

- Multiselección de categorías: al seleccionar 2 categorías, ambas quedan marcadas como activas simultáneamente (antes, seleccionar la segunda desactivaba la primera). Confirmado por color de chip.
- Filtro de fechas: fijar "desde" a una fecha futura vacía la lista (2 → 0 tarjetas); al quitar la fecha, vuelve a mostrar los eventos.
- Orden alfabético: confirmado que reordena los títulos correctamente.
- Vista de mapa: al cambiar a "Mapa" se oculta la lista y se renderiza un canvas de deck.gl con los eventos filtrados como marcadores.
- Geolocalización automática ("Cerca de mí"): activa sin errores con una ubicación simulada; no se filtró ningún evento porque ambos estaban dentro del radio de 5 km.

## Fuera de alcance (documentado, no construido)

- Geolocalización **manual** (introducir una dirección a mano) — requeriría un geocodificador externo (ej. Nominatim/OSM o Google Geocoding), decisión de proveedor pendiente.

## Pendiente antes de marcar como Hecho

- [ ] Probar en producción con más de 2 eventos reales para confirmar visualmente el comportamiento de filtros combinados.
- [ ] Validación del responsable del contrato.
- [ ] Enlazar esta evidencia al issue #91.
