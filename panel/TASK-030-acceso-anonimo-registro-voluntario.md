# TASK-030 — Acceso sin autenticación por defecto, registro voluntario (REQ-059)

**Categoría:** Gestión Usuarios
**Requisito origen:** REQ-059 (PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 1
**Labels:** auth, ux, verificacion
**Estado:** En progreso — verificación técnica completa, pendiente de validación

## Descripción técnica

Acceso sin autenticación por defecto en modo consulta, con opción de registro voluntario.

## Verificación realizada (contra el código y el Drupal real, sin cambios necesarios)

- **Permisos Drupal**: el rol `anonymous` solo tiene `access content` (comprobado con `drush php:eval` sobre `Role::load('anonymous')->getPermissions()`) — suficiente y correcto para consulta pública de contenido publicado, sin permisos de escritura ni administración.
- **Front (`02-26-web-front`)**: ninguna ruta tiene `canActivate` (verificado con `grep` en todo `src/app`) — no existe ningún guard de autenticación que bloquee el acceso a página alguna.
- **`AuthService`** gestiona una cuenta de portal opcional (`CuentaPortal`), completamente independiente de la navegación: `estaAutenticado()` devuelve `false` sin credenciales guardadas, sin que ninguna página lo use para redirigir o bloquear.
- **Funciones que sí piden registro voluntario, verificadas una por una**:
  - `ValoracionUsuarioComponent`: el bloque de valoraciones es visible sin sesión; solo al intentar enviar una valoración se pide iniciar sesión (`"Inicia sesión para dejar tu valoración"`).
  - `MiCuentaPage`: sin sesión, muestra un mensaje con botones "Iniciar sesión"/"Crear cuenta"; no es una pantalla de error ni un bloqueo.
  - Encuestas (`encuesta`/`respuesta_encuesta`): requieren cuenta para registrar la respuesta (evita respuestas duplicadas/anónimas descontroladas), pero ver las encuestas y resultados no lo requiere.
- **"Mi Plan de Viaje"**: funciona íntegramente sin cuenta, guardado en `localStorage` (`plan-viaje.service.ts`) — ni siquiera esta función, pensada para personalización, exige registro.

## Entregable / Evidencia

Este documento, con la verificación punto por punto de permisos Drupal + ausencia de guards + comportamiento real de cada función que toca autenticación.

## Pendiente antes de marcar como Hecho

- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #80
