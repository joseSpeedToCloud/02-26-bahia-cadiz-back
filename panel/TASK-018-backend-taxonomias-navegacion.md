# TASK-018 — Taxonomías, categorías jerárquicas y estructuras de navegación

**Categoría:** CMS
**Requisito origen:** REQ-029 (SPEC-001)
**Prioridad:** High
**Story Points:** 3 *(estimado)*
**Labels:** backend, cms, drupal, taxonomia
**Estado:** En progreso

## Descripción técnica

Definir las taxonomías y categorías jerárquicas necesarias (tipo de evento, tipo de playa, modo de movilidad, municipio, etc.) y las estructuras de navegación asociadas, manteniendo una separación clara entre datos y presentación (el CMS no debe acoplarse a cómo el portal/app renderiza el contenido).

## Entregable / Evidencia

Vocabularios de taxonomía dados de alta + documento de mapeo taxonomía → navegación del portal.

**Avance:**

- Vocabularios dados de alta vía `scripts/provision-content-types.php`: `municipio` (6 municipios de la Mancomunidad), `categoria_evento`, `servicios_playa`, `etiquetas_semanticas` (libre, UNE 178503).
- Pendiente: documento de mapeo taxonomía → navegación, que depende de que exista el Portal (fuera de alcance de este panel de backend, ver `panel/README.md`).
