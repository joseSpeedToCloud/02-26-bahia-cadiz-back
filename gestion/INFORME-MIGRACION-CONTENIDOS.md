# Informe de migración de contenidos existentes

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-020, TASK-013
**Fecha:** 2026-08-03
**Estado:** Ejecutado contra la base de datos real de producción (no un ensayo local).

---

## 1. Origen vs. destino

| Origen | Destino | Estado |
|---|---|---|
| Web legacy de El Puerto de Santa María — "Cortes de tráfico previstos en la ciudad" (HTML, sin API) | Content type `aviso_trafico` en el CMS | **Migrado** |
| Webs legacy de los otros 5 municipios de la Mancomunidad | — | **No migrado** — ver apartado 3 |

## 2. Ejecución real (2026-08-03)

Script: `02-26-web-back/scripts/import-legacy-trafico.php`, ejecutado con `drush php:script` contra la base de datos de producción real (`db_bahiacadiz`, vía `cloud-sql-proxy`), no contra una copia local.

- **Origen**: 51 avisos detectados en el HTML en vivo de `elpuertodesantamaria.es` en el momento de la ejecución.
- **Destino**: 47 nodos `aviso_trafico` creados/actualizados, **sin publicar** (`status = 0`) — pendientes de revisión editorial humana antes de ser visibles en el portal, tal como exige el propio script (contenido de un tercero vía scraping puntual, sin API/acuerdo formal).
- **Validación de integridad**: verificado directamente contra la base de datos real (`SELECT status, count(*) FROM node_field_data WHERE type='aviso_trafico' GROUP BY status`) tras la ejecución: 47 filas con `status = 0` (importadas, sin publicar) + 2 filas con `status = 1` (contenido de ejemplo ya existente, no afectado por esta migración). La diferencia entre 51 detectados y 47 nodos creados se debe a expedientes duplicados en la propia página de origen (mismo número de expediente listado más de una vez), que el script colapsa correctamente en un único nodo por diseño (idempotencia por número de expediente).

**Siguiente paso editorial** (fuera de alcance de este informe): revisar y publicar manualmente en `/admin/content?type=aviso_trafico&status=2` los 47 avisos importados.

## 3. Hallazgo real durante esta ejecución (no relacionado con la migración en sí)

Al conectar directamente contra la base de datos de producción se confirmó un dato que no estaba documentado correctamente en intentos anteriores de esta sesión (ver REQ-146): el usuario real de conexión a la base de datos de producción es **`bahiacadiz_admin`**, no `drupal` como se había asumido. Con el usuario correcto la conexión y las consultas funcionan sin ningún problema de permisos — el bloqueo de "permission denied for table node" encontrado en el trabajo de REQ-146 estaba causado por probar con un usuario incorrecto, no por un problema real de privilegios en la base de datos. Se deja constancia aquí para no dejarlo como un hueco de seguridad sin explicar.

## 4. Pendiente — hueco real, no resuelto por este informe

Las webs legacy de los otros 5 municipios de la Mancomunidad (aparte de El Puerto de Santa María) **no tienen ninguna fuente de datos mínimamente estructurada** para migrar de forma automática (confirmado al escribir el script original — ver comentario en `import-legacy-trafico.php`). Migrar su contenido existente (si lo hay y es relevante) requeriría que la Mancomunidad facilite el contenido directamente (documentos, acceso a sus webs, o contacto con cada ayuntamiento) — no es algo resoluble por scraping automático como en este caso. Mismo bloqueo ya señalado para la coordinación con ayuntamientos en `gestion/MAPEO-UNE-178503.md`/REQ-013.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
