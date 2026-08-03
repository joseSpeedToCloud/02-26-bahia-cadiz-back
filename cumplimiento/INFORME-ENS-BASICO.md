# Informe de cumplimiento — Esquema Nacional de Seguridad (categoría básica)

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Referencia normativa:** Real Decreto 311/2022, de 3 de mayo, por el que se regula el Esquema Nacional de Seguridad (ENS)
**Categoría declarada:** Básica
**Requisitos origen:** REQ-010, REQ-149, OPS-008
**Fecha:** 2026-07-29
**Estado:** Medidas técnicas implementadas y verificadas contra la infraestructura real. Marco organizativo/documental **pendiente** (ver apartado 4).

---

## 1. Alcance

Este informe cubre la infraestructura y aplicación de la Plataforma Digital Bahía de Cádiz: portal web (Angular/Ionic), CMS headless (Drupal 11), base de datos (Cloud SQL PostgreSQL), almacenamiento de ficheros (Cloud Storage) y la infraestructura GCP que los soporta (proyecto `id-2190759668-26`), desplegados vía Terraform (`02-26-infra-terraform`).

No cubre: el equipamiento/red de los propios ayuntamientos miembros de la Mancomunidad, ni servicios de terceros fuera del control de este contrato (p. ej. el hosting de las webs municipales legacy).

---

## 2. Medidas técnicas implementadas (verificadas contra infraestructura real)

### 2.1 Marco operacional — Control de acceso

| Medida | Implementación | Evidencia |
|---|---|---|
| Autenticación de usuarios | Drupal core (roles/usuarios) | `web/modules/custom/bahia_multisitio` |
| Mínimo privilegio (IAM) | Ningún rol `owner`/`editor` en cuentas de servicio gestionadas por Terraform; roles acotados (`secretAccessor`, `run.invoker`, `cloudsql.client`, `artifactregistry.reader/writer`, `storage.objectViewer/objectAdmin`) | `Fase0/iam.tf`, `Fase1/*/main.tf`, `Fase2/*/main.tf` (verificado por grep sobre todo el repo, sin coincidencias de roles amplios) |
| Mínimo privilegio (personas) | ⚠️ **Desactualizado** — declarado como "un único Owner" el 2026-07-29, pero verificado el 2026-08-03 contra el IAM real del proyecto: **los 3 miembros del equipo (Ana Tormo, Arnaldo Morales, Jose Sánchez) tienen actualmente `roles/owner`**. No cumple el principio de mínimo privilegio declarado. Pendiente de decisión del equipo sobre a quién quitar el rol. | `gcloud projects get-iam-policy id-2190759668-26`, verificado 2026-08-03 |
| Segregación de contenido por origen | Aislamiento de edición por municipio (`hook_node_access`) — un editor municipal solo puede modificar contenido de su propio municipio | `web/modules/custom/bahia_multisitio/bahia_multisitio.module`; verificado en producción con 6 combinaciones nodo×editor y prueba de usuario real |
| Restricción de dominios IAM | Organization Policy `iam.allowedPolicyMemberDomains` restringida a `speedtocloud.com` (Customer ID `C01k16zbv`) | `Fase0/org_policies.tf`, aplicado en real (`terraform apply`, 2026-07-29) |
| Prevención de claves de larga duración | Organization Policy `iam.disableServiceAccountKeyCreation` | `Fase0/org_policies.tf`, aplicado en real |

### 2.2 Marco de protección — Cifrado

| Medida | Implementación | Evidencia |
|---|---|---|
| Cifrado en tránsito (BD) | Cloud SQL con `ssl_mode = "ENCRYPTED_ONLY"` (rechaza conexiones no cifradas) | `Fase2/3.CloudSQL/main.tf` |
| Cifrado en tránsito (web) | Cloud Run solo sirve HTTPS (TLS gestionado por la plataforma) | Configuración por defecto de Cloud Run |
| Cifrado en reposo | Cifrado por defecto de Google Cloud (Cloud SQL, Cloud Storage) — always-on, no configurable ni desactivable | Comportamiento estándar de la plataforma GCP |
| Uniform bucket-level access | Organization Policy `storage.uniformBucketLevelAccess` (fuerza IAM en vez de ACLs por objeto en buckets nuevos) | `Fase0/org_policies.tf`, aplicado en real |

### 2.3 Marco de protección — Registro de actividad (trazabilidad)

| Medida | Implementación | Evidencia |
|---|---|---|
| Cloud Audit Logs — Data Access | Habilitado `DATA_READ`/`DATA_WRITE` sobre CloudSQL, Cloud Storage y Secret Manager | `Fase0/iam.tf` (`google_project_iam_audit_config` × 3), confirmado en el estado real de Terraform |
| Admin Activity logs | Habilitado por defecto por GCP (no desactivable) | N/A |

### 2.4 Marco operacional — Continuidad y disponibilidad

| Medida | Implementación | Evidencia |
|---|---|---|
| Copias de seguridad | Cloud SQL: backups diarios automáticos, retención de 30 | `Fase2/3.CloudSQL/main.tf` (`backup_configuration`); confirmado con `gcloud sql backups list` — backups reales `SUCCESSFUL` desde 2026-07-16 |
| Prueba de restauración | Clonado real de la instancia desde backup (`gcloud sql instances clone`), `STATUS: DONE` | Ver REQ-146; verificación de contenido de tablas pendiente por un problema de permisos de rol en la BD, no relacionado con el backup en sí |
| Monitorización de disponibilidad (SLA 99,5%) | Uptime checks + alertas de caída sobre front y back | `Fase2/4.Monitoring/main.tf`, aplicado en real |
| Alertas de rendimiento | Alertas de latencia p95 > 3000ms (umbral comprometido en la oferta) | `Fase2/4.Monitoring/main.tf` |
| Dashboard de disponibilidad | Cloud Monitoring Dashboard (uptime, latencia, instancias activas) | `Fase2/4.Monitoring/dashboard.tf`, aplicado en real 2026-07-29 |
| Autoescalado | Cloud Run `min_instances=0`, `max_instances=10` en front y back | `Fase2/1.Cloud-Run-Front/main.tf`, `Fase2/2.Cloud-Run-Back/main.tf`; verificado con prueba de carga real (`scripts/load-test.py` en `02-26-infra-terraform`) |

### 2.5 Marco de protección — Comunicaciones

| Medida | Implementación | Evidencia |
|---|---|---|
| CORS restringido | `allowedOrigins` limitado a la URL real de producción del portal, sin wildcard | `02-26-web-back/web/sites/default/services.yml` |
| Bloqueo de rutas de instalación en producción | `install.php`/`update.php`/`authorize.php` bloqueados a nivel de Apache | `02-26-web-back/Dockerfile` |

---

## 3. Categoría básica del ENS — checklist de medidas mínimas (Anexo II RD 311/2022)

| Dimensión | Medidas mínimas categoría básica | Estado |
|---|---|---|
| Marco organizativo | Política de seguridad, normativa de seguridad, procedimientos de seguridad, proceso de autorización | ❌ **No existe documentación formal** — ver apartado 4 |
| Marco operacional | Planificación, control de acceso, explotación, monitorización | ✅ Implementado a nivel técnico (ver 2.1, 2.4) |
| Medidas de protección | Instalaciones, personal, equipos, comunicaciones, información, servicios | ✅ Implementado a nivel técnico (ver 2.2, 2.3, 2.5) — protección del "personal" (formación, roles de seguridad formales) **no cubierta** |

---

## 4. Pendiente — hueco real, no resuelto por este informe

Este informe documenta **controles técnicos**, que están genuinamente implementados y verificados. El ENS categoría básica exige además un **marco organizativo formal** que no existe todavía en este proyecto:

- Política de seguridad de la información aprobada formalmente.
- Designación formal de roles ENS (Responsable de la Información, Responsable del Servicio, Responsable de Seguridad) — distintos de los roles de IAM técnico ya asignados.
- Procedimiento de gestión de incidentes de seguridad.
- Análisis de riesgos formal.
- Procedimiento de autorización de cambios en producción.

Estos son documentos/decisiones de gobernanza, no tareas de código — necesitan la participación de la Mancomunidad (como responsable último del servicio) y de SpeedToCloud a nivel organizativo, no solo de implementación técnica. No se han redactado en este informe porque inventar roles/políticas sin que la organización real los apruebe no tendría validez — quedan como pendiente explícito.

---

## 5. Conclusión

Las medidas técnicas exigidas por la categoría básica del ENS para los marcos operacional y de protección están **implementadas y verificadas contra la infraestructura real** (no solo declaradas en código sin aplicar). El marco organizativo (políticas, roles ENS formales, procedimientos) está **pendiente** y requiere una decisión de gobernanza conjunta entre SpeedToCloud y la Mancomunidad antes de poder declarar cumplimiento ENS categoría básica completo.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
