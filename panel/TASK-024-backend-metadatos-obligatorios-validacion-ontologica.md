# TASK-024 — Metadatos semánticos obligatorios + validación ontológica antes de producción

**Categoría:** CMS
**Requisito origen:** REQ-036 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.1)
**Prioridad:** Alta
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, une-178503, semantica, calidad-datos
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.1, cita literal: *"Todos los contenidos gestionados por el CMS incorporarán metadatos semánticos obligatorios. El adjudicatario deberá realizar una validación ontológica completa antes de la puesta en producción, garantizando la coherencia con la norma UNE 178503."*

Dos exigencias distintas, ambas verificadas y cerradas aquí:
1. **"Obligatorios"** — no basta con que el campo exista (eso ya lo cubrían TASK-018/019); tiene que ser imposible guardar contenido turístico sin metadatos semánticos.
2. **"Validación ontológica completa antes de producción"** — un mecanismo real que compruebe el contenido real, no solo la definición de campos.

## Auditoría real (2026-08-07)

Ninguna de las dos partes estaba cubierta:

- **Campo no obligatorio**: los 6 `ensure_field(..., 'field_*_etiquetas', ...)` de `provision-content-types.php` se llamaban sin el parámetro `$required`, que por defecto es `FALSE`. Confirmado en config real: los 5 campos turísticos tenían `required: false`.
- **Sin validación ontológica**: no existía ningún script ni proceso que comprobara el contenido real contra el modelo UNE 178503 antes de desplegar.
- **Contenido de ejemplo real incumpliendo ya**: 6 nodos (2 transporte, 2 aparcamiento, 1 playa extra, 1 ruta extra) no tenían ningún término semántico asignado — confirmado con el propio validador nuevo, no solo revisando el código del seed.

## Trabajo realizado

**1. Campo obligatorio de verdad:**
- `ensure_field()` generalizado (mismo patrón que ya se aplicó dos veces antes para `settings` desincronizados, REQ-027/030): ahora también compara y corrige `required` en campos ya existentes, no solo al crearlos. Sin esto, marcar `required: TRUE` en el código no tiene ningún efecto en instalaciones que ya tenían el campo creado como opcional — **bug real, reproducido y confirmado** antes de corregirlo.
- Los 5 campos turísticos (`evento`, `playa`, `ruta`, `transporte`, `aparcamiento`) pasan a `required: TRUE`.
- `aviso_trafico` se deja **explícitamente excepcionado y documentado en el código**: es un dato operativo/transitorio (un corte de vía), no una ficha del catálogo turístico — mismo criterio que TASK-016 ya aplicó para excluirlo de multiidioma.

**2. Contenido de ejemplo corregido con criterio honesto** (no relleno genérico): se añadió el término `Movilidad urbana` al vocabulario (para poder etiquetar con honestidad recursos de movilidad que no tienen ningún atributo turístico real — p. ej. una zona azul de pago sin dato de accesibilidad), y se completaron las etiquetas de los 6 nodos que faltaban basándose en sus datos reales (p. ej. "El Vapor" → `Sostenible, Aire libre, Movilidad urbana`; "Playa de la Barrosa" → `Accesible, Aire libre`, porque `field_playa_accesible: TRUE` en sus propios datos).

**3. Validación ontológica real** (`scripts/validar-ontologia-une178503.php`, nuevo): recorre **todo** el contenido real (no una muestra) de los 5 tipos turísticos y comprueba (a) que cada nodo tiene metadatos semánticos, (b) que cada término referenciado pertenece de verdad al vocabulario `etiquetas_semanticas`, y (c) integridad del grafo de las taxonomías jerárquicas (sin padres rotos ni ciclos). Termina con `exit(1)` si hay incumplimientos, pensado como gate de despliegue (Cloud Build) — se ejecuta también desde `entrypoint.sh` de forma informativa (no bloquea el arranque: un dato de contenido incorrecto es un problema editorial, no una caída de servicio).

## Hallazgo real durante la verificación (documentado, no oculto)

Al ejecutar la corrección contra la base de datos local de desarrollo (acumulada tras muchísimas iteraciones de esta sesión), 2 de los 6 nodos (`L-7 Cádiz – San Fernando`, `Zona Azul Plaza de España`) parecían no guardar el cambio. Investigado a fondo: `$node->save()` devolvía `false` para **cualquier** campo en esos nodos concretos, no solo etiquetas — un problema de estado/revisiones corruptas acumulado en esa instalación local, no un bug del código. Confirmado de forma definitiva reconstruyendo desde una base de datos completamente limpia (`docker compose down -v`): ahí la corrección funciona sin ningún problema. Documentado por si vuelve a aparecer en otro entorno de desarrollo con mucho histórico local.

## Entregable / Evidencia

Verificado real (no solo "debería funcionar"), en una instalación limpia desde cero:

- Log de aprovisionamiento: `[field] evento.field_evento_etiquetas -> corregido (required)` (y los otros 4 tipos) — confirma que el fix de `ensure_field()` corrige el campo retroactivamente.
- **Prueba funcional real en el formulario de edición**: capturada con Playwright — el campo aparece como `Etiquetas semanticas (UNE 178503) *` (con asterisco de obligatorio) en `/node/add/evento`, y un intento de guardar el formulario sin rellenarlo es rechazado por Drupal (no redirige a la ficha creada, se queda en el formulario).
- **Validación ontológica real ejecutada**: `Nodos turisticos comprobados: 10 / Incumplimientos encontrados: 0 / [OK] Validacion ontologica superada: coherencia con UNE 178503 confirmada.`
- Config exportada y copiada al repo (`config/sync/field.field.node.*.field_*_etiquetas.yml`, los 5 con `required: true`).
- Entorno local reconstruido desde cero (`docker compose down -v` + `up -d --build`) y desmontado tras la verificación.

Pendiente exclusivamente de validación por el responsable del contrato.
