# Mapeo semántico del modelo de contenidos contra UNE 178503

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-013 (apartado "auditoría de contenido y mapeo hacia la ontología UNE 178503")
**Referencia normativa:** UNE 178503:2022 — Destinos Turísticos Inteligentes. Semántica aplicada a turismo (AENOR/SEGITTUR)
**Fecha:** 2026-07-30

---

## 1. Qué exige UNE 178503

UNE 178503 define un vocabulario, taxonomías y ontología en español para representar la información de un destino turístico, apoyado en el vocabulario base de **schema.org** extendido para turismo (mantenido por SEGITTUR, `semantica.segittur.es`). No define una lista cerrada de clases: modela tipos de recurso (alojamiento, restauración, cultura, ocio, eventos, infraestructura...) con **atributos comunes transversales** (ubicación, contacto, clasificación, idioma, valoración, fechas, oferta comercial) y permite que un mismo recurso tenga varias clasificaciones simultáneas.

Fuentes: [UNE 178503:2022 (norma)](https://www.une.org/encuentra-tu-norma/busca-tu-norma/norma?c=N0068062), [SEGITTUR — semántica turística](https://semantica.segittur.es/), [ESMARTCITY — nuevas normas UNE](https://www.esmartcity.es/2019/08/05/asociacion-espanola-normalizacion-publica-dos-nuevas-normas-destinos-turisticos-inteligentes).

## 2. Estado previo (auditoría)

Antes de este mapeo, el proyecto solo declaraba alineación con la norma de forma genérica (`especificaciones/SPEC-001-plataforma-bahia-cadiz.md`: "Alineada con la norma UNE 178503") y tenía implementado, en código, un campo `field_*_etiquetas` (taxonomía libre `etiquetas_semanticas`) en la mayoría de content types — pero sin un documento que explicitara la correspondencia real campo a campo. Este documento cierra ese hueco.

## 3. Mapeo campo a campo por tipo de contenido

Atributos comunes UNE 178503/schema.org marcados entre paréntesis.

### Evento (`node/evento`) → clase `MusicEvent`/`Event`

| Campo Drupal | Atributo UNE 178503 / schema.org |
|---|---|
| `title` | `name` |
| `field_evento_descripcion` | `description` |
| `field_evento_fecha_inicio` | `startDate` |
| `field_evento_fecha_fin` | `endDate` |
| `field_evento_direccion`, `field_evento_latitud/longitud` | `location` (`streetAddress`, `latitude`, `longitude`) |
| `field_evento_imagen` | `image` |
| `field_evento_precio` | `offers.price` |
| `field_evento_enlace` | `url` |
| `field_evento_accesible` | atributo de accesibilidad (extensión turística, sin equivalente directo en schema.org core) |
| `field_evento_organizador` | `organizer` |
| `field_evento_categoria`, `field_evento_etiquetas` | `touristType` / clasificación temática |

### Playa (`node/playa`) → clase `TouristAttraction`/`Landform`

| Campo Drupal | Atributo UNE 178503 / schema.org |
|---|---|
| `title` | `name` |
| `field_playa_descripcion` | `description` |
| `field_playa_latitud/longitud` | `geo` (`latitude`, `longitude`) |
| `field_playa_imagen` | `image` |
| `field_playa_accesible` | atributo de accesibilidad |
| `field_playa_servicios` | `amenityFeature` |
| `field_playa_estado_mar`, `field_playa_bandera`, `field_playa_ocupacion` | datos dinámicos/tiempo real — extensión propia sobre la clase base, sin equivalente normativo directo (UNE 178503 no cubre estado marítimo) |
| `field_playa_etiquetas` | `touristType` / clasificación temática |

### Ruta (`node/ruta`) → clase `TouristTrip`

| Campo Drupal | Atributo UNE 178503 / schema.org |
|---|---|
| `title` | `name` |
| `field_ruta_descripcion` | `description` |
| `field_ruta_tipo` | `touristType` (modalidad: ciclista/senderismo/peatonal/accesible/urbana) |
| `field_ruta_distancia_km`, `field_ruta_duracion_min` | `itinerary`/`distance` (extensión) |
| `field_ruta_lat_inicio/fin`, `field_ruta_lng_inicio/fin` | `geo` de origen/destino |
| `field_ruta_track` | representación GIS del itinerario (fuera de schema.org, formato GPX/KML estándar) |
| `field_ruta_imagen` | `image` |
| `field_ruta_etiquetas` | `touristType` / clasificación temática |

### Transporte (`node/transporte`) → clase `BusStation`/`TrainStation`/`Service` (según `field_transporte_modo`)

| Campo Drupal | Atributo UNE 178503 / schema.org |
|---|---|
| `title` | `name` |
| `field_transporte_descripcion` | `description` |
| `field_transporte_modo` | selecciona la subclase (`BusStation`/`TrainStation`/servicio marítimo) |
| `field_transporte_operador` | `provider` |
| `field_transporte_horario_resumen` | `openingHours` (resumen; el detalle vive en GTFS, formato estándar de transporte, no UNE 178503) |
| `field_transporte_url_oficial` | `url` |
| `field_transporte_tarifa_resumen` | `offers.price` |
| `field_transporte_latitud/longitud` | `geo` |
| `field_transporte_imagen` | `image` |
| `field_transporte_etiquetas` | `touristType` / clasificación temática |

### Aparcamiento (`node/aparcamiento`) → clase `ParkingFacility`

| Campo Drupal | Atributo UNE 178503 / schema.org |
|---|---|
| `title` | `name` |
| `field_aparcamiento_descripcion` | `description` |
| `field_aparcamiento_tipo` | clasificación de la instalación |
| `field_aparcamiento_capacidad` | `maximumAttendeeCapacity` (aproximado) |
| `field_aparcamiento_tarifa` | `offers.price` |
| `field_aparcamiento_horario` | `openingHours` |
| `field_aparcamiento_accesible` | atributo de accesibilidad |
| `field_aparcamiento_latitud/longitud` | `geo` |
| `field_aparcamiento_imagen` | `image` |
| `field_aparcamiento_etiquetas` | `touristType` / clasificación temática |

### Aviso de tráfico y Parte meteorológico

Ambos tipos son **información operativa/dinámica de movilidad y meteorología**, no recursos turísticos en el sentido de UNE 178503 (que modela destinos/recursos, no incidencias puntuales ni previsiones). No se les asigna clase de la ontología por ese motivo — quedan fuera de alcance de la norma, y así se documenta explícitamente para no forzar un mapeo artificial. `aviso_trafico` y `parte_meteorologico` no llevan campo `_etiquetas` en el modelo actual, de forma consistente con esta exclusión.

## 4. Multi-tipado

Siguiendo el principio de multi-tipado de la norma (un recurso puede tener varias clasificaciones simultáneas), el campo `field_*_etiquetas` (taxonomía `etiquetas_semanticas`, cardinalidad múltiple) presente en evento/playa/ruta/transporte/aparcamiento es el mecanismo real en el modelo de datos para asignar clasificaciones adicionales sin necesidad de cambiar el bundle del nodo.

## 5. Pendiente

Este mapeo es una correspondencia semántica documental. Quedan fuera de este documento (no resueltos aquí):

- **Exposición serializada como JSON-LD/schema.org** en las respuestas de la API (hoy el JSON:API expone los campos de Drupal, no un `@context`/`@type` de schema.org) — ya señalado como trabajo futuro en el propio código (`field_playa_latitud`: "exposicion JSON-LD/schema.org (TASK-019)"). Corresponde a TASK-019, no a REQ-013.
- Validación formal de este mapeo contra la especificación completa de la ontología (el acceso público a `semantica.segittur.es` documenta atributos y clases de alto nivel, no el fichero de ontología OWL/RDF completo).

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
