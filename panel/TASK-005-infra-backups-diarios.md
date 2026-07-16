# TASK-005 — Backups diarios con retención de 30 días

**Categoría:** Soporte/Garantía
**Requisito origen:** REQ-146 (SPEC-001), relacionado con REQ-086
**Prioridad:** High
**Story Points:** 3
**Labels:** infra, terraform, backup
**Estado:** Pendiente

## Descripción técnica

Configurar Cloud SQL/Storage con copias de seguridad automáticas diarias y política de retención de 30 días, conforme al ratio comprometido en la oferta.

## Entregable / Evidencia

Política de backup en Terraform (`google_sql_database_instance.backup_configuration`) + prueba de restauración.
