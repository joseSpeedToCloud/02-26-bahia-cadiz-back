# TASK-036 — PWA: offline de datos y notificaciones push (REQ-066)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-066 (issue #87 / PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 5
**Labels:** pwa, push, offline, service-worker
**Estado:** En progreso — implementado y en verificación local

## Descripción técnica

PWA con carga inicial rápida, navegación offline básica, instalación desde navegador (Add to Home Screen), notificaciones push y actualización automática de la caché.

## Auditoría inicial (qué ya cumplía y qué no)

- ✅ **Carga inicial rápida**: Lighthouse real (22 julio 2026) — Rendimiento 82/100, Accesibilidad/Buenas Prácticas/SEO 100/100 (`lighthouse-reports/`).
- ✅ **Instalación desde navegador**: `manifest.webmanifest` completo, `display: standalone`, set completo de iconos.
- ✅ **Actualización automática de caché**: comportamiento por defecto del Service Worker de Angular (`registerWhenStable:30000`).
- ❌ **Navegación offline básica**: `ngsw-config.json` solo cacheaba el *shell* de la app, sin `dataGroups` — el contenido (JSON:API) no se guardaba para consulta offline.
- ❌ **Notificaciones push**: no implementado en absoluto.

## Lo añadido en esta task

### Offline de datos (dataGroup)

- `02-26-web-front/ngsw-config.json`: nuevo `dataGroups` (`contenido-jsonapi`), estrategia `freshness` (red primero, caché si no hay conexión), `maxAge: 3d`, apuntando a `https://gcp-crs-back-02-26-bh-*.run.app/jsonapi/**` (y `/en/jsonapi/**`). Verificado que aparece en el `ngsw.json` generado por `ng build`.

### Notificaciones push

**Backend (`02-26-web-back`, módulo `bahia_cadiz_api`)**:
- `bahia_cadiz_api.install`: tabla `bahia_cadiz_push_subscripciones` (endpoint, claves p256dh/auth).
- `PushController::suscribir`/`desuscribir`: `POST`/`DELETE /api/portal/push/suscribir`, **sin autenticación requerida** (REQ-059: consulta y funciones básicas sin cuenta).
- `bahia_cadiz_api.module`: `hook_node_insert`/`hook_node_update` envían push a todos los suscritos cuando un `evento` pasa a publicado, vía `minishlink/web-push` (composer, MIT).
- Claves VAPID leídas de `VAPID_PUBLIC_KEY`/`VAPID_PRIVATE_KEY` (variables de entorno) — si no están configuradas, se omite el envío sin romper el guardado del nodo.

**Frontend (`02-26-web-front`)**:
- `PushNotificationsService`: envuelve `SwPush` (Angular), pide la suscripción con la clave pública VAPID y la registra en el backend.
- Interruptor "Avisos de nuevos eventos" en `Mi cuenta`, visible **sin necesidad de sesión iniciada**.
- Clave pública VAPID inyectada en runtime igual que `apiUrl` (`env.js` + `docker-entrypoint.sh`), no hardcodeada en el bundle de producción.

**Infraestructura (`02-26-infra-terraform`, Fase2)**:
- Nuevas variables `vapid_public_key`/`vapid_private_key` (esta última sensible, sin autogenerar — debe ser un par de claves real, no un valor aleatorio).
- Secreto `gcp-sms-vapidpriv-${cliente_id}` en Secret Manager, solo si se proporciona la clave.
- `VAPID_PUBLIC_KEY` inyectada como env var normal en **ambos** Cloud Run (front y back); `VAPID_PRIVATE_KEY` solo en el back, vía Secret Manager.
- El binding IAM de `secretAccessor` ya era genérico (itera `extra_secret_env_vars`), así que el nuevo secreto queda cubierto sin tocar IAM aparte.
- `terraform validate` en verde.

## Cómo generar las claves VAPID (una sola vez)

```bash
npx web-push generate-vapid-keys --json
```

Guardar `publicKey` en `var.vapid_public_key` (no sensible) y `privateKey` en `var.vapid_private_key` (por `terraform.tfvars`, nunca commiteado).

## Pendiente antes de marcar como Hecho

- [ ] Verificación local end-to-end (suscripción real + push real recibido en el navegador)
- [ ] Generar y aplicar las claves VAPID reales en el proyecto GCP real (`terraform apply`, decisión del equipo, no aplicado por mí)
- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #87
