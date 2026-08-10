# TASK-014 — Administración simplificada y permisos avanzados por rol

**Categoría:** CMS
**Requisito origen:** REQ-021, REQ-023 (SPEC-001)
**Prioridad:** High
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, permisos
**Estado:** En progreso (parcial) — corregido el 2026-08-10, la documentación decía "Pendiente" pero la matriz de roles ya estaba hecha desde el 2026-08-03

## Descripción técnica

Configurar una administración de contenidos sencilla e intuitiva, pensada para un usuario medio de ofimática (no técnico), junto con un sistema de permisos por rol que permita segmentar qué puede editar/publicar cada perfil de usuario (por municipio, por tipo de contenido, etc.).

## Entregable / Evidencia

Matriz de roles y permisos documentada en `gestion/MATRIZ-ROLES-PERMISOS.md`, verificada contra la configuración real de `02-26-web-back` (`config/sync/user.role.*.yml`, `user.flood.yml`, `user.settings.yml`):

- 3 roles reales: `administrator` (equipo técnico), `content_editor` (rol estándar de Drupal), `editor_municipio` (custom, TASK-017 — restringido a su propio municipio vía `hook_node_access()`).
- Políticas de seguridad de acceso reales (REQ-025): protección contra fuerza bruta, alta de usuarios solo por administrador, caducidad de enlaces de restablecimiento (24h), sesiones HTTPS-only. Complejidad de contraseña forzada y 2FA identificadas como no implementadas (módulos contrib no instalados), documentado honestamente.
- Formularios de creación de contenido en español con textos de ayuda, editor CKEditor, sin paso de "publicar/sincronizar" adicional.

**Sigue pendiente**: la propia matriz señala que el entregable exige una **prueba real con un usuario no técnico** validando el flujo de edición — no se puede simular ni fabricar, requiere una persona real (idealmente un futuro editor municipal) y no es resoluble unilateralmente. Mismo tipo de bloqueo que las pruebas de usabilidad de REQ-015.
