# TASK-025 — Acceso a los recursos correctamente geolocalizados en el mapa interactivo (Agenda)

**Categoría:** CMS / Geolocalización
**Requisito origen:** REQ-039 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 2 *(estimado)*
**Labels:** backend, frontend, geolocalizacion, mapa
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2 (Portal web), cita literal: *"Recursos georreferenciados en mapas interactivos; geolocalización del usuario + recursos cercanos; filtrado sencillo."* REQ-039 cubre específicamente la parte de "acceso a la información de los recursos... correctamente geolocalizados y visualizados mediante un mapa interactivo, gestionados vía CMS" (la geolocalización del propio usuario y el filtrado son requisitos distintos del checklist, no cubiertos aquí).

Esta capacidad ya estaba implementada de forma general en [TASK-021](TASK-021-backend-geolocalizacion-mapas.md) (REQ-028): los 6 tipos de contenido turístico tienen campos de latitud/longitud gestionables desde el CMS (formularios de administración de Drupal), expuestos vía JSON:API, y el mapa interactivo del portal (`mapa.page.ts`, deck.gl) los renderiza como capas.

## Auditoría real (2026-08-10) — hueco encontrado, no asumido por la documentación previa

No se dio por bueno el "Hecho" de TASK-021 sin comprobar los datos reales. Auditoría contra JSON:API real (local y producción):

- **playa, ruta, transporte, aparcamiento, aviso_trafico**: coordenadas reales presentes en todos los nodos comprobados. Correcto.
- **evento**: los 2 nodos de ejemplo ("Feria de Mayo de Cádiz" y "Concierto en la Playa de la Victoria"), tanto en local como **en producción**, tenían `field_evento_latitud`/`field_evento_longitud` **vacíos** — nunca se rellenaron al crear el contenido de ejemplo (`scripts/seed-example-content.php`), a diferencia del resto de tipos.
- Consecuencia real: `mapa.page.ts` filtra explícitamente por coordenadas no nulas antes de pintar cada capa (`capaCirculo()`, línea `data: datos.filter((d) => d.latitud != null && d.longitud != null)`) — sin excepción ni error visible. Los eventos de la Agenda **nunca han aparecido en el mapa interactivo**, ni en local ni en producción, desde que existe esa capa. No se detectó antes porque ninguna verificación previa (incluida la de TASK-021) comprobó específicamente la capa de eventos con datos reales.

## Corrección aplicada

`scripts/seed-example-content.php` (idempotente, se ejecuta en cada arranque del contenedor vía `entrypoint.sh`, en todos los entornos):

1. Añadidas `field_evento_latitud`/`field_evento_longitud` a los `upsert_node()` de ambos eventos de ejemplo (para instalaciones nuevas).
2. Añadida una sección de corrección para instalaciones ya existentes (mismo patrón que la corrección de etiquetas semánticas de REQ-036): rellena las coordenadas solo si el campo está vacío, sin pisar un valor editorial real.
3. Coordenadas usadas, documentadas con criterio honesto:
   - "Concierto en la Playa de la Victoria" → misma coordenada exacta que el nodo real "Playa de la Victoria" (`36.506400`, `-6.274500`) — la dirección del evento es literalmente esa playa.
   - "Feria de Mayo de Cádiz" → coordenada aproximada dentro de Cádiz capital (`36.523000`, `-6.279000`). Se investigó si "Recinto Ferial El Rinconcillo" corresponde a una ubicación real y verificable; no se encontró ningún recinto ferial documentado con ese nombre en Cádiz capital (el propio evento es contenido de ejemplo, no un dato del catálogo oficial de la Mancomunidad) — se documenta explícitamente como aproximada en vez de aparentar una precisión que no existe.

## Hallazgo colateral (no corregido en esta task)

Durante la verificación local se reprodujo el mismo bug ya documentado en [TASK-024](TASK-024-backend-metadatos-obligatorios-validacion-ontologica.md) (`$node->save()` devuelve `false` silenciosamente para un nodo concreto tras acumular muchos reinicios de Docker) — confirmado de nuevo como corrupción local acumulada, no un bug de código, resuelto con `docker compose down -v` + reconstrucción limpia. No requiere acción de código.

## Entregable / Evidencia

- **Antes**: `curl` a JSON:API de producción confirma `field_evento_latitud`/`longitud` = `null` en ambos eventos.
- **Después (verificado en local, entorno limpio tras `docker compose down -v` + `up -d --build`)**: ambos eventos con coordenadas reales; capa "Eventos" del mapa muestra 2 marcadores nuevos (color azul oscuro, `EVENTO_COLOR`) en la zona real de Cádiz capital — captura de pantalla con Playwright, sin errores de consola.
- **Pendiente**: la corrección solo se aplica en el próximo despliegue de este código a producción (el script es idempotente y se ejecuta en cada arranque del contenedor vía `entrypoint.sh`, igual que el resto de correcciones de contenido de esta sesión) — no se ha parcheado la base de datos de producción directamente, para no modificar datos en un sistema compartido sin que el responsable del contrato lo autorice explícitamente.
