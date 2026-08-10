# TASK-018 — Taxonomías, categorías jerárquicas y estructuras de navegación

**Categoría:** CMS
**Requisito origen:** REQ-029 (SPEC-001)
**Prioridad:** High
**Story Points:** 3 *(estimado)*
**Labels:** backend, cms, drupal, taxonomia
**Estado:** Hecho

## Descripción técnica

Definir las taxonomías y categorías jerárquicas necesarias (tipo de evento, tipo de playa, modo de movilidad, municipio, etc.) y las estructuras de navegación asociadas, manteniendo una separación clara entre datos y presentación (el CMS no debe acoplarse a cómo el portal/app renderiza el contenido).

## Auditoría real (2026-08-05)

Al retomar esta task para cerrarla se comprobó el estado real, no lo que ya documentaba esta misma task de una sesión anterior: los vocabularios existían, pero **ninguno era realmente jerárquico** — `ensure_term()` en `provision-content-types.php` nunca fijaba una relación padre/hijo, así que "categorías jerárquicas" no se cumplía pese al título de esta task. Además, el Portal (ya construido) consumía las taxonomías solo como texto de solo lectura (etiquetas/chips estáticos en las páginas de detalle), nunca como navegación o filtro — por lo que el documento de mapeo taxonomía→navegación, de haberse escrito entonces, habría descrito un mapeo hacia nada.

## Trabajo realizado

**Backend** (`02-26-web-back/scripts/provision-content-types.php`):
- Nueva función `ensure_term_parent($vid, $hijo, $padre)`: fija la relación padre/hijo real de Drupal (campo `parent` del término) de forma idempotente.
- Vocabulario `categoria_evento` reestructurado en 2 niveles, sin renombrar ni borrar ningún término existente (ya referenciados por contenido real):
  - **Cultura y ocio** (nuevo, padre) → Cultura, Música, Ocio familiar (existentes, ahora hijos)
  - **Gastronomía y tradición** (nuevo, padre) → Gastronomía, Ferias y fiestas (existentes, ahora hijos)
  - **Deporte** — se deja como categoría de primer nivel, no encajaba de forma natural en ninguno de los dos grupos.
- El resto de vocabularios (`municipio`, `servicios_playa`, `etiquetas_semanticas`) se dejan planos a propósito: `municipio` es una lista geográfica cerrada sin sub-regiones que aportar, `servicios_playa` es un checklist de equipamientos (no una taxonomía temática), y `etiquetas_semanticas` es explícitamente etiquetado libre alineado con UNE 178503 (forzar jerarquía ahí iría contra su propio diseño).

**Frontend** (`02-26-web-front`):
- `TaxonomiaService` (nuevo, `src/app/services/taxonomia.service.ts`): lee la jerarquía real vía JSON:API (`/jsonapi/taxonomy_term/categoria_evento?include=parent`) y la expone como árbol. Drupal representa la raíz de un vocabulario con un recurso especial `id: "virtual"` en la relación `parent` — así se distingue una categoría raíz de una hija real.
- Página "Agenda de Eventos" (`eventos.page.ts`/`.html`): chips de categoría (los 3 nodos raíz: Cultura y ocio, Gastronomía y tradición, Deporte) que **filtran de verdad** la lista — seleccionar un padre incluye los eventos de sus categorías hijas, no solo los etiquetados literalmente con el nombre del padre. Antes las categorías solo se mostraban como texto estático sin ninguna interacción.

## Documento de mapeo taxonomía → navegación

| Vocabulario | Jerárquico | Uso en el Portal |
|---|---|---|
| `categoria_evento` | Sí (2 niveles) | Filtro por chips en "Agenda de Eventos" (`/tabs/eventos`) — nuevo con esta task |
| `municipio` | No (lista plana) | Texto de ubicación en listados/detalle de evento, playa, ruta, transporte, aparcamiento, aviso de tráfico |
| `servicios_playa` | No (checklist) | Chips informativos en el detalle de playa |
| `etiquetas_semanticas` (UNE 178503) | No (libre) | Reservado para metadatos/SEO — sin UI de navegación propia todavía (ver TASK-019, pendiente) |

Separación de datos y presentación: verificada — el backend expone JSON:API de solo lectura (`jsonapi.settings.yml`: `read_only: true`) sin ningún acoplamiento a cómo el Portal renderiza nada; toda la lógica de presentación (agrupación en árbol, chips, filtrado) vive en el front.

## Entregable / Evidencia

Verificado con Playwright en local: los 3 chips de categoría raíz aparecen en "Agenda de Eventos", clicar "Cultura y ocio" filtra correctamente a los eventos con Cultura/Música/Ocio familiar (o el propio "Cultura y ocio" si algún evento se etiquetara directamente con el padre), clicar de nuevo quita el filtro. Confirmado contra JSON:API real que la relación `parent` se expone correctamente (términos raíz con `parent.data[0].id === "virtual"`, hijos con el UUID real de su padre).
