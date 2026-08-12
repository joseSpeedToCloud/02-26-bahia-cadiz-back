# TASK-028 — Cuentas públicas del portal: registro, preferencias, encuestas, valoraciones, borrado de cuenta

**Categoría:** Gestión Usuarios
**Requisito origen:** REQ-043 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 11 *(feature nueva de arquitectura, no un ajuste; incluye recuperación de contraseña + resultados de encuestas)*
**Labels:** backend, frontend, usuarios, rgpd
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2, cita literal: *"Acceso sin autenticación en modo consulta; registro necesario para guardar prioridades, preferencias, encuestas y valoraciones; derecho a eliminación de la cuenta de usuario."*

## Auditoría real (2026-08-10) — hueco total, no parcial

Antes de implementar nada se comprobó el estado real: el frontend no tenía ninguna pantalla de login/registro, el backend tenía `register: admin_only` (sin auto-registro) y el rol `anonymous` solo tenía permisos de lectura. No existía ningún tipo de contenido para encuestas o valoraciones de usuario — el campo `field_*_valoracion` conectado en REQ-042 es editorial (lo introduce el editor del CMS), no una valoración enviada por un ciudadano; ya se había anotado explícitamente como fuera de alcance en TASK-020. Se preguntó al usuario cómo abordarlo (completo / mínimo / pausar) — eligió implementarlo completo.

## Decisiones de diseño (y por qué)

- **Cuentas públicas separadas de los roles de CMS**: nuevo rol `usuario_portal` (ciudadano), sin ningún permiso de edición de contenido — no toca ni debilita `register: admin_only` (que sigue protegiendo el alta de cuentas con permisos de CMS a través del formulario nativo `/user/register`). El registro público usa una vía completamente distinta (ver más abajo).
- **Registro/borrado de cuenta via endpoint propio, no JSON:API**: Drupal no expone JSON:API de alta para `user--user` a un anónimo (crear una cuenta es una operación de "administer users", no de entidad genérica), ni una operación de "cancelar cuenta". Se añadió `PortalUserController` (módulo `bahia_cadiz_api`) con `POST /api/portal/registro`, `GET/PATCH/DELETE /api/portal/mi-cuenta`.
- **HTTP Basic Auth, no cookies de sesión**: el front y el back viven en orígenes Cloud Run distintos; las cookies de sesión no cruzan ese límite sin complicaciones adicionales de SameSite/CORS. Las credenciales se guardan en `sessionStorage` (no `localStorage`) — se pierden al cerrar la pestaña, compromiso deliberado entre comodidad y no dejar una contraseña indefinidamente en el navegador.
- **Valoraciones y respuestas de encuesta SÍ son JSON:API estándar** (`node--valoracion_usuario`, `node--respuesta_encuesta`): contenido normal con permisos `create X content` para `usuario_portal` — solo el propio usuario (`user--user`) necesitaba un contrato a medida.
- **Publicación inmediata de valoraciones/respuestas**: `hook_node_presave()` en `bahia_cadiz_api.module` fuerza `status = published` para esos dos bundles — sin esto, `status` se queda en su valor de fábrica y nadie vería una valoración hasta que un administrador la revisara nodo a nodo, algo que no pide el requisito (que exige acceso inmediato tras registrarse).
- **Borrado de cuenta = eliminación real e inmediata**, no el flujo de cancelación con email de confirmación de Drupal core (`user_cancel_confirm`): ese flujo está pensado para formularios HTML, no para una SPA desacoplada. Se usa `$user->delete()` directamente, protegido por el permiso real de Drupal `cancel account` (no `cancel own user account`, que es solo el título legible, no el nombre de máquina — error real cometido y corregido durante la verificación).

## Bugs reales encontrados y corregidos durante la implementación (no solo "debería funcionar")

- `User::loadByProperties()` no existe como método estático de la entidad (solo `load()`/`create()` lo son) — corregido a `\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(...)`.
- Permiso mal escrito: `cancel own user account` no es un nombre de permiso real de Drupal (es el título legible); el nombre de máquina correcto es `cancel account`. Se descubrió porque Drupal rechazó explícitamente el permiso al guardar el rol.
- Falta el `FieldStorageConfig` de `field_encuesta_pregunta` (solo se creó el `FieldConfig`) — Drupal lo rechazó con un error claro, corregido.
- **JSON:API estaba configurado como solo lectura a nivel de sitio** (`jsonapi.settings:read_only: true`) — nunca hizo falta escribir hasta ahora. Cambiado a `false` en `config/sync/jsonapi.settings.yml` (config exportada, no un ajuste puntual que se revertiría en el próximo `config:import`). Sigue protegido por los permisos normales de Drupal: solo `usuario_portal` puede crear `valoracion_usuario`/`respuesta_encuesta`, nadie más puede escribir nada nuevo por esta vía.

## Hallazgo colateral importante para todo el proyecto (no solo REQ-043)

Verificado empíricamente (repetido 3 veces, incluido con un tipo de contenido normal y revisionado como `playa`, y comprobado a nivel de fila SQL en Postgres, no solo a través de la API de entidades): **en esta instalación de Drupal 11, borrar cualquier cuenta de usuario borra en cascada todo el contenido que esa cuenta creó**, sea cual sea su tipo. Es una característica real del core de Drupal (no un hook de este proyecto — no se encontró ningún `hook_user_delete`/`hook_ENTITY_TYPE_predelete` propio ni de contrib que lo explique), y es genuinamente útil para RGPD en el caso de `usuario_portal` (borrado real, no solo desvinculación). **Pero aplica igual a los roles de edición del CMS** (`editor_municipio`, `content_editor`): si se borra la cuenta de un editor municipal, su contenido (eventos, playas, etc.) se borraría también, no se conservaría ni se transferiría a Anónimo. Recomendación operativa para la Mancomunidad: bloquear (`status = 0`) una cuenta de editor que deba dejar de tener acceso, no borrarla, si su contenido debe conservarse. Anotado aquí porque no estaba documentado en ningún sitio del proyecto hasta ahora.

## Verificación real (2026-08-10/11)

Contra el backend local (nunca contra producción, por tratarse de creación/borrado real de cuentas de usuario):

- `POST /api/portal/registro`: cuenta real creada (201), rechaza nombre/correo duplicado y contraseña corta.
- `GET /api/portal/mi-cuenta` (Basic Auth): devuelve los datos reales de la cuenta (200).
- `PATCH /api/portal/mi-cuenta`: preferencias ("temas de interés") guardadas y devueltas correctamente.
- `POST /jsonapi/node/valoracion_usuario`: valoración real creada (201), publicada automáticamente, autoría atribuida al usuario real.
- `POST /jsonapi/node/respuesta_encuesta`: respuesta real creada (201).
- `DELETE /api/portal/mi-cuenta`: cuenta borrada de verdad (confirmado que ya no existe en la tabla de usuarios).
- **Con navegador real (Playwright) contra la UI real**, no solo la API: flujo completo registro → `/mi-cuenta` (preferencias guardadas y visibles) → ficha de playa real (valoración con estrellas enviada, "Gracias por tu valoración." visible) → `/encuestas` (respuesta enviada, "Gracias por tu respuesta." visible) → eliminar cuenta desde la propia UI (confirmación + redirección a inicio). Sin errores de consola en ningún paso.
- Entorno de prueba limpiado tras la verificación: nodo de playa devuelto a borrador, encuesta/valoraciones/respuestas y usuarios de prueba borrados, CORS temporal revertido en el contenedor (el fichero real nunca se tocó, confirmado con `git status`).

## Ampliación (2026-08-12): recuperación de contraseña real + resultados de encuestas

A petición del usuario tras probar el login en producción, dos piezas más:

### Recuperación de contraseña ("olvidaste tu contraseña")

Bloqueo real encontrado antes de construir nada: **este proyecto nunca había enviado un correo real** — el contenedor no tiene ningún MTA (`sendmail`/`msmtp`), y `system.mail.yml` apuntaba a un `sendmail` inexistente. Se resolvió con:

- SMTP real (contraseña de aplicación de Gmail de `notificaciones@srv.speedtocloud.com`), activado en `settings.php` solo si hay credenciales (`getenv('SMTP_USER')`), usando el plugin `symfony_mailer` que ya trae el core de Drupal 11 (`$config['system.mail']['interface']['default'] = 'symfony_mailer'` + `mailer_dsn`) — no ha hecho falta ningún módulo contrib de correo.
- `POST /api/portal/olvide-contrasena` (`PortalUserController`): reutiliza el mecanismo real de Drupal core (`_user_mail_notify('password_reset', $user)`, el mismo enlace de un solo uso y caducidad que usa `/user/password`) — no se reinventa el token. El enlace del correo abre una página de Drupal (no del portal Angular) para poner la contraseña nueva; después hay que volver a iniciar sesión en el portal con ella — limitación de UX honesta, no oculta.
- Responde siempre el mismo mensaje genérico, exista o no la cuenta (no revela qué correos están registrados).
- Credenciales inyectadas por variable de entorno (`SMTP_USER`/`SMTP_PASS`), nunca en código: `.env` local (gitignored) para desarrollo, Secret Manager + Terraform para producción (ver más abajo).

### Resultados agregados de encuestas

El pliego solo exige "registro para... encuestas" (ya cumplido) y expone "encuestas" como parte del catálogo REST general — no exige resultados agregados ni avisos por email. Se implementó lo mínimo que hace la funcionalidad realmente útil: `GET /api/portal/encuestas/{uuid}/resultados` (público, cuenta agregados de respuestas por opción vía consulta interna con `accessCheck(FALSE)`, sin abrir permiso de lectura general sobre `respuesta_encuesta` — así no se expone quién respondió qué, solo el recuento). El frontend muestra una barra de porcentaje por opción justo después de responder.

### Bugs reales adicionales encontrados y corregidos

- `\Drupal::entityQuery('node')` no resolvía los campos del bundle correctamente en este entorno — corregido a `\Drupal::entityTypeManager()->getStorage('node')->getQuery()`, el patrón ya usado en el resto del proyecto.
- **Hallazgo de proceso importante**: un `docker cp` de un fix aplicado directamente a un contenedor en ejecución (patrón usado varias veces esta sesión para pruebas rápidas) **no persiste en la imagen** — al recrear el contenedor sin `--build`, reaparece el código antiguo. Esto hizo reaparecer temporalmente el bug de `field_encuesta_pregunta` sin `FieldStorageConfig` que ya se había corregido el día anterior. Resuelto reconstruyendo la imagen (`docker compose up -d --build`) antes de repetir la verificación.
- **Gap de proceso más importante**: los tipos de contenido/campos/rol de REQ-043 se crearon vía script en tiempo de ejecución pero **nunca se exportaron a `config/sync`** (al contrario que el resto de scripts `provision-*.php` de este proyecto, que sí siguen la convención documentada en `CLAUDE.md` de exportar tras ejecutar). Consecuencia real: cada `drush config:import` (rama de actualización de `entrypoint.sh`) los borra, y el script los vuelve a crear justo después en el mismo arranque — funciona, pero es fragil y quedó demostrado por el bug de arriba. Corregido: `drush config:export` ejecutado y las ~28 YAML nuevas añadidas a `config/sync/` (incluye también los campos `field_*_valoracion` de REQ-042, que tenían el mismo gap sin que nadie lo hubiera notado).
- Al exportar, `jsonapi.settings.yml` volvió a `read_only: true` (el valor activo de ese contenedor no coincidía con el fichero) — corregido de nuevo explícitamente.

### Infraestructura (`02-26-infra-terraform`)

Nuevo secreto en Secret Manager, siguiendo exactamente el mismo patrón ya usado para `google_oauth_client_secret` (credencial externa real, no autogenerada): `Fase2/secrets.tf` (`google_secret_manager_secret.smtp_pass`), `Fase2/variables.tf` (`smtp_user`, `smtp_pass`), `Fase2/main.tf` (inyectado en Cloud Run Back — `SMTP_USER` como env var normal, `SMTP_PASS` por `secret_key_ref`; el acceso IAM al secreto ya lo concede genéricamente el módulo `2.Cloud-Run-Back` a cualquier secreto pasado por `extra_secret_env_vars`, sin cambios adicionales). `terraform validate` y `terraform plan` reales ejecutados: 3 recursos a crear (secreto, versión, permiso IAM), 0 a destruir.

## Entregable / Evidencia

**Backend** (`02-26-web-back`):
- `scripts/provision-usuarios-portal.php` (nuevo): rol `usuario_portal`, tipos de contenido `encuesta`/`respuesta_encuesta`/`valoracion_usuario`, campo `field_user_temas_interes`.
- `web/modules/custom/bahia_cadiz_api/src/Controller/PortalUserController.php` (nuevo) + `bahia_cadiz_api.routing.yml` (nuevo) + `bahia_cadiz_api.module` (nuevo, hook de publicación inmediata).
- `config/sync/jsonapi.settings.yml`: `read_only: false`.
- `entrypoint.sh`: script enganchado en ambas ramas (instalación nueva y actualización).

**Frontend** (`02-26-web-front`):
- `src/app/services/auth.service.ts` (nuevo), `src/app/services/participacion.service.ts` (nuevo).
- `src/app/pages/{login,registro,mi-cuenta,encuestas,olvide-contrasena}/` (nuevas).
- `src/app/components/valoracion-usuario/valoracion-usuario.component.ts` (nuevo), integrado en las 5 fichas de detalle.
- Accesos desde la portada (`home.page.html`): "Encuestas" y "Mi cuenta".

**Infraestructura** (`02-26-infra-terraform`): `Fase2/secrets.tf`, `Fase2/variables.tf`, `Fase2/main.tf` — secreto SMTP, pendiente de `terraform apply` por el usuario.

Pendiente exclusivamente de validación por el responsable del contrato.
