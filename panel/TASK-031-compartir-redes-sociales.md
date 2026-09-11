# TASK-031 — Compartir en redes sociales (REQ-061)

**Categoría:** Integración
**Requisito origen:** REQ-061 (PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 1
**Labels:** integracion, redes-sociales, verificacion
**Estado:** En progreso — verificación técnica completa, pendiente de validación

## Descripción técnica

Posibilidad de compartir información a través de las principales redes sociales.

## Verificación realizada (ya implementado, sin cambios necesarios)

`CompartirComponent` (`02-26-web-front/src/app/components/compartir/compartir.component.ts`):

- Usa el **Web Share API nativo** (`navigator.share`) cuando el dispositivo/navegador lo soporta (móvil, PWA instalada) — delega en el propio selector nativo del sistema operativo, que incluye todas las redes sociales instaladas (WhatsApp, Instagram, Telegram, X, Facebook, correo, etc.).
- Si no está disponible (navegador de escritorio), muestra botones directos a **WhatsApp, Facebook y X** con enlaces de compartición estándar (`wa.me`, `facebook.com/sharer`, `twitter.com/intent/tweet`), sin necesidad de SDK ni credenciales de terceros.
- **Cableado y verificado en las 5 fichas de detalle** (`grep` confirma su uso real, no huérfano): playa, evento, ruta, transporte y aparcamiento.

## Entregable / Evidencia

Este documento + código ya existente en `compartir.component.ts`/`.html`, verificado como realmente utilizado (no huérfano) en las 5 páginas de detalle del portal.

## Pendiente antes de marcar como Hecho

- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #82
