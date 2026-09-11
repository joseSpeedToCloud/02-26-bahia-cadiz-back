# TASK-032 — Seguridad: conexiones seguras y permisos mínimos (REQ-062)

**Categoría:** Seguridad/RGPD
**Requisito origen:** REQ-062 (issue #83 / PPT ap. 4.2, texto literal: *"Seguridad de la solución mediante conexiones seguras hacia/desde los servicios de contenidos, solicitando solo los permisos necesarios."*) — el contenido ya estaba trazado bajo REQ-010/REQ-015/REQ-021/REQ-025/REQ-149, sin que ningún documento citara literalmente "REQ-062"
**Prioridad:** Alta
**Story Points:** 2
**Labels:** seguridad, tls, iam, verificacion
**Estado:** En progreso — verificación técnica completa, un pendiente conocido sin resolver

## Descripción técnica

Seguridad de la solución mediante conexiones seguras hacia/desde los servicios de los contenidos, solicitando solo permisos necesarios.

## Verificación realizada (contra código real, no solo documentación previa)

Confirma de forma independiente lo ya declarado en `panel/TASK-008-seguridad-iam-ens-rgpd.md`, `cumplimiento/INFORME-ENS-BASICO.md`, `gestion/PRUEBAS-TECNICAS-SEGURIDAD-RENDIMIENTO.md` y `gestion/MATRIZ-ROLES-PERMISOS.md`:

1. **HTTPS de extremo a extremo**: front→back siempre por la URL real de Cloud Run (`https://` garantizado por la plataforma, `Fase2/main.tf:106` inyecta `DRUPAL_API_URL` desde `module.cloud_run_back.service_url`); back→Cloud SQL vía conector nativo cifrado, `ssl_mode = "ENCRYPTED_ONLY"` (`Fase2/3.CloudSQL/main.tf:62-63`).
2. **CORS**: `allowedOrigins` restringido a un único origen real de producción, sin wildcard ni localhost (`02-26-web-back/web/sites/default/services.yml:223`).
3. **IAM**: ninguna cuenta de servicio con `roles/owner`/`roles/editor` en ningún `.tf` del repo — todos los bindings son roles acotados (`run.invoker`, `cloudsql.client`, `secretmanager.secretAccessor`, `artifactregistry.reader/writer`).
4. **Roles Drupal**: anónimo y autenticado sin permisos administrativos, sin `bypass node access` (`config/sync/user.role.anonymous.yml`, `user.role.authenticated.yml`).
5. **Permisos del navegador**: geolocalización solo bajo demanda (clic explícito o al activar el filtro de cercanía en el mapa, `mapa.page.ts:199-233`) — nunca al arrancar la app. Sin `getUserMedia` ni `Notification.requestPermission` en ningún punto del código.
6. **Secretos**: ninguno en texto plano en ficheros versionados de estos 3 repos.

## Pendiente conocido (ya documentado en `INFORME-ENS-BASICO.md`, no resuelto)

- Los 3 humanos del equipo mantienen `roles/owner` en el proyecto GCP real — no gestionado por Terraform, fuera del alcance del código. Si REQ-062 se interpreta también sobre accesos humanos (no solo cuentas de servicio), sigue pendiente.

## Nota de higiene (hallazgo nuevo, no bloqueante)

- `02-26-infra-terraform/Fase0/terraform.tfvars` contiene un token real de GitHub en texto plano — el fichero **no está versionado** (excluido en `.gitignore`), así que no incumple el requisito, pero conviene rotar el token si se ha compartido alguna vez fuera de git.

## Entregable / Evidencia

Este documento, cruzando referencias con `TASK-008`, `INFORME-ENS-BASICO.md`, `PRUEBAS-TECNICAS-SEGURIDAD-RENDIMIENTO.md` y `MATRIZ-ROLES-PERMISOS.md`.

## Pendiente antes de marcar como Hecho

- [ ] Decidir si se revocan los `roles/owner` humanos (o se sustituyen por roles acotados + acceso puntual elevado)
- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #83
