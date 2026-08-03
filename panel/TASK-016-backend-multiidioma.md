# TASK-016 — Gestión multilingüe con interfaz de traducción integrada

**Categoría:** CMS
**Requisito origen:** REQ-024 (SPEC-001)
**Prioridad:** High
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, i18n
**Estado:** Hecho

## Descripción técnica

Configurar la gestión multilingüe del contenido (español + idiomas turísticos relevantes, p. ej. inglés) con una interfaz de traducción integrada en el propio flujo editorial, sin depender de exportar/importar archivos externos.

## Entregable / Evidencia

**Implementado y probado en local (2026-08-03)**: `02-26-web-back/scripts/provision-multiidioma.php` (idempotente, integrado en `entrypoint.sh` junto al resto de aprovisionamiento). Habilita `language` + `content_translation`, añade español e inglés, y activa Content Translation en los 5 tipos de contenido orientados al visitante (evento, playa, ruta, transporte, aparcamiento) — se excluyen `aviso_trafico`/`parte_meteorologico` por ser avisos operativos, no fichas turísticas. Solo los campos `*_descripcion` son traducibles; el resto (coordenadas, fechas, taxonomías, imágenes) se comparte entre idiomas para no duplicar datos objetivos.

**Hallazgo real corregido durante la implementación**: el sitio se instaló sin el módulo de idiomas, por lo que Drupal usó `en` como idioma por defecto de fábrica pese a que todo el contenido está en español — los 65 nodos existentes tenían `langcode=en` de forma incorrecta. Corregido: español pasa a ser el idioma por defecto real (`system.site.default_langcode`), los 65 nodos se reetiquetaron a `es`, y se configuraron los prefijos de URL (`es` sin prefijo, `/en/` para inglés).

**Prueba end-to-end real**: nodo real "Playa de la Victoria" (playa, nid 3) traducido a "Victoria Beach" en inglés. Verificado vía JSON:API: `/jsonapi/node/playa/{uuid}` devuelve el original en español (`langcode: es`), `/en/jsonapi/node/playa/{uuid}` devuelve la traducción real en inglés (`langcode: en`), y el campo no traducible `field_playa_latitud` es idéntico en ambos (36.506400) — confirma que los datos compartidos no se duplican ni se pierden.

Pendiente: interfaz de selector de idioma en el portal (front) — fuera de alcance de esta task (backend). Validación del responsable del contrato (Mancomunidad).
