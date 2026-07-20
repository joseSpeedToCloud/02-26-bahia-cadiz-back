# TASK-005 — Backups diarios con retención de 30 días

**Categoría:** Soporte/Garantía
**Requisito origen:** REQ-146 (SPEC-001), relacionado con REQ-086
**Prioridad:** High
**Story Points:** 3
**Labels:** infra, terraform, backup
**Estado:** Hecho

## Descripción técnica

Configurar Cloud SQL/Storage con copias de seguridad automáticas diarias y política de retención de 30 días, conforme al ratio comprometido en la oferta.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform/Fase2/3.CloudSQL`**: backups diarios de la instancia Cloud SQL con 30 días de retención, confirmado explícitamente en el README de ese repo ("Backups diarios de Cloud SQL con 30 días de retención (obligatorio, PPT cláusula 4.7)").

Pendiente: la prueba de restauración real — no hay evidencia de que se haya ejecutado un `restore` de prueba todavía.
