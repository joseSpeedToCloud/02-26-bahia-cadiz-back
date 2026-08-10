# TASK-016 — Gestión multilingüe con interfaz de traducción integrada

**Categoría:** CMS
**Requisito origen:** REQ-024 (SPEC-001), también cubre REQ-032 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.1, control de traducciones pendientes)
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

## Verificación adicional — control de traducciones pendientes (REQ-032, 2026-08-06)

REQ-032 (issue #53) pide explícitamente, además de la traducción en sí, "control de traducciones pendientes" (PPT ap. 4.1: "...incorporando las funcionalidades necesarias para la realización de traducciones, control de traducciones pendientes, etc."). No estaba verificado en la evidencia original de esta task, así que se comprobó de forma específica antes de dar por cerrado REQ-032.

Estos content types tienen a la vez Content Translation **y** Content Moderation activos (flujos editoriales, REQ-024/TASK-015) — Drupal core desactiva el control manual de "traducción desactualizada" cuando ambos coexisten en algunas configuraciones, así que no se dio por hecho que funcionara solo por estar ambos módulos habilitados.

Verificado real sobre el nodo "Playa de la Victoria" (nid 3): el control "Flag other translations as outdated" existe en el formulario de edición del idioma original; al marcar la traducción inglesa como desactualizada (`ContentTranslationManager::getTranslationMetadata()->setOutdated(TRUE)`), la vista `/node/3/translations` pasa a mostrar el estado real **"Published outdated"** en la fila de English — el editor ve de un vistazo qué traducciones han quedado atrás cuando se actualiza el original. Confirma que REQ-032 queda cubierto por este trabajo, sin necesidad de desarrollo adicional.
