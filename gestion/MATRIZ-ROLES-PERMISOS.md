# Matriz de roles y permisos — administración de contenidos

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-021, TASK-014 (relacionado con REQ-023/REQ-025)
**Fecha:** 2026-08-03
**Estado:** Documentación de la matriz completa. Prueba con usuario no técnico pendiente — ver apartado 3.

---

## 1. Roles reales configurados

| Rol | Alcance | Permisos clave |
|---|---|---|
| `administrator` | Equipo técnico | `is_admin: true` — acceso total, sin restricciones (bypass de todas las comprobaciones de permisos) |
| `content_editor` | Editor general (rol estándar de Drupal) | Ver resumen de contenido, gestionar alias de URL, ver/revertir revisiones, ver su propio contenido sin publicar |
| `editor_municipio` | Editor municipal (custom, TASK-017) | Crear/editar/borrar los 7 tipos de contenido (evento, playa, ruta, transporte, aparcamiento, aviso_trafico, parte_meteorologico), ver/revertir revisiones — **restringido a su propio municipio** por `hook_node_access()` en `web/modules/custom/bahia_multisitio/` |

El rol `editor_municipio` es el que de verdad usarán los perfiles no técnicos de los ayuntamientos: puede crear y editar contenido de su municipio, pero no puede tocar el de otro municipio ni acceder a configuración del sitio.

## 2. Por qué se considera "sencillo e intuitivo para un usuario medio de ofimática"

- **Tema de administración**: Claro, el tema moderno de Drupal 11 pensado explícitamente para simplificar la experiencia de edición (sustituye al antiguo tema "Seven", más técnico).
- **Formularios de creación de contenido**: cada campo tiene una etiqueta en español y, cuando el dato no es obvio, un texto de ayuda ya escrito (ej. campo "Precio": *"Ej: 'Gratuito', '10 EUR', 'Desde 5 EUR'"*; campo "Tarifa" de aparcamiento: *"Texto libre, ej. '~1€/hora, máx. 3h'"*) — ver `02-26-web-back/scripts/provision-content-types.php`.
- **Editor de texto enriquecido** (CKEditor, incluido en Drupal core) para los campos de descripción — experiencia equivalente a un procesador de texto (negrita, listas, enlaces), no markup técnico.
- **Sin pasos de publicación adicionales**: al guardar un nodo, aparece automáticamente en el portal (confirmado en `gestion/MANUAL-USO-WEB.md`) — no hay un paso de "desplegar" o "sincronizar" que un usuario no técnico pueda olvidar.

## 3. Pendiente — prueba con usuario no técnico

El propio entregable de TASK-014 exige una **prueba real con un usuario no técnico** validando el flujo de edición — esto no se puede simular ni fabricar: requiere una persona real (idealmente uno de los futuros editores municipales) sin conocimientos técnicos usando el CMS de verdad y confirmando que le resulta manejable. Mismo tipo de bloqueo que las "pruebas de usabilidad con usuarios reales" ya señaladas en REQ-015 — no resoluble unilateralmente por SpeedToCloud.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
