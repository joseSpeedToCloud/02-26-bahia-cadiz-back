# TASK-008 — Políticas IAM y cifrado conforme a ENS categoría básica

**Categoría:** Seguridad/RGPD
**Requisito origen:** REQ-149 (SPEC-001), relacionado con REQ-010
**Prioridad:** Highest
**Story Points:** 8
**Labels:** infra, seguridad, ens, rgpd
**Estado:** En progreso (parcial)

## Descripción técnica

Definir roles IAM de mínimo privilegio, cifrado en reposo y en tránsito, y registro de auditoría (Cloud Audit Logs) para cumplir el Esquema Nacional de Seguridad categoría básica y el RGPD/LOPDGDD.

## Entregable / Evidencia

**Ya cumplido:**

- IAM de mínimo privilegio por recurso concreto (no un módulo genérico, pero sí aplicado): `roles/cloudsql.client` solo al Cloud Run que lo necesita (`Fase2/3.CloudSQL`), `roles/secretmanager.secretAccessor` solo al service agent de Cloud Build que lo necesita (`Fase0/iam.tf`).
- Secretos (contraseña BD, hash salt, admin password) nunca en texto plano ni en env vars visibles — todos en Secret Manager, inyectados por `secret_key_ref`.
- Cifrado en reposo: por defecto de GCP (AES-256) en Cloud SQL/GCS/Secret Manager, sin configuración adicional necesaria.
- Cifrado en tránsito de Cloud SQL: **confirmado en código**, no solo documentado — `ssl_mode = "ENCRYPTED_ONLY"` en `Fase2/3.CloudSQL/main.tf:63`.
- **Cloud Audit Logs (Data Access)** aplicados en real sobre `cloudsql`/`storage`/`secretmanager` (`Fase0/iam.tf`, `terraform apply` confirmado).
- **CORS restringido** al dominio real del front (`services.yml`, ya no `*`).

**Sigue pendiente:**

- **Organization Policies de GCP**: no hay ningún recurso `google_organization_policy`/`google_org_policy_policy` en ningún fichero del repo — no configuradas en absoluto (más allá del IAM de mínimo privilegio, que sí está aplicado). Este es un hueco propio, distinto del IAM. Propuestas pendientes de confirmar: `iam.disableServiceAccountKeyCreation`, `storage.uniformBucketLevelAccess`, `iam.allowedPolicyMemberDomains`.
- Informe formal de cumplimiento ENS básico: no existe como documento todavía.
