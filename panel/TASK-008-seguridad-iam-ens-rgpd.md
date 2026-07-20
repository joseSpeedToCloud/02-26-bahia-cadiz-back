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

**Sigue pendiente:**
- Cifrado en tránsito explícito de Cloud SQL: verificar que `ssl_mode = ENCRYPTED_ONLY` (mencionado en el README de `02-26-infra-terraform`) esté realmente aplicado y no solo documentado.
- Cloud Audit Logs: sin configuración explícita encontrada (ni habilitación ni exportación a un sink).
- CORS del backend sigue abierto a `*` (`services.yml`) — pendiente restringir al dominio real, ver README de `02-26-web-back`.
- Informe formal de cumplimiento ENS básico: no existe como documento todavía.
