# TASK-017 — Soporte multisitio para los municipios de la Mancomunidad

**Categoría:** CMS
**Requisito origen:** REQ-023 (SPEC-001)
**Prioridad:** Medium *(estimado)*
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, multisitio
**Estado:** Hecho

## Descripción técnica

Configurar el soporte multisitio del CMS (p. ej. Domain Access u otra estrategia equivalente) para que cada municipio miembro de la Mancomunidad pueda gestionar su propio contenido diferenciado dentro de la misma instancia, compartiendo el core y las taxonomías comunes.

## Entregable / Evidencia

Al menos 2 "sitios" de municipios distintos funcionando sobre la misma instancia, con contenido y permisos diferenciados.

**Implementado** (enfoque elegido: aislamiento por permisos/rol sobre la taxonomía "Municipio" compartida, en vez de Domain Access — la plataforma ya es headless y de portal único, así que "sitios" separados por dominio no aporta nada; lo que de verdad hace falta es que cada ayuntamiento edite solo lo suyo):

- `web/modules/custom/bahia_multisitio/` — módulo custom con `hook_node_access()`: deniega editar/borrar contenido (Evento, Playa, Ruta, Transporte, Aparcamiento) cuyo municipio no coincida con el asignado al usuario editor. El personal de la Mancomunidad (rol `administrator`) no se ve afectado (permiso `bypass municipio access check`).
- `scripts/provision-multisitio.php` — campo `field_user_municipio` en usuarios, rol `editor_municipio` con permisos de creación/edición/borrado sobre los 5 tipos de contenido, y 2 editores de ejemplo (`editor.cadiz`, `editor.sanfernando`) con permisos diferenciados sobre su propio municipio — evidencia literal del entregable.
- `entrypoint.sh` y `config/sync/core.extension.yml` actualizados para aprovisionar y activar esto automáticamente en cada despliegue.

**Verificado en real** (producción, tras corregir un bug de comparación de tipos en el hook — `term->id()` como string vs `int` en `in_array()` estricto):

- Vía `drush php:script` directo contra la BD real: 6 combinaciones de nodo × editor probadas (municipio único y multi-municipio), todas con el resultado correcto.
- Vía navegador: `editor.cadiz` logueado, menú de administración visible (permiso `access navigation` añadido), edición de contenido de su propio municipio permitida y de otro municipio denegada — confirmado por el usuario.
