# TASK-006 — Monitorización y alertas de disponibilidad (SLA 99,5%)

**Categoría:** Soporte/Garantía
**Requisito origen:** REQ-147 (SPEC-001), relacionado con REQ-086 y REQ-127
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, monitoring, sla
**Estado:** En progreso (código escrito, pendiente de aplicar)

## Descripción técnica

Configurar Cloud Monitoring con uptime checks y alertas (email/Slack) para el cumplimiento del SLA de disponibilidad comprometido en la oferta y del tiempo de carga máximo de 3 segundos.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform/Fase2/4.Monitoring`** (módulo nuevo, enganchado en `Fase2/main.tf`):

- 2 uptime checks (portal front `/`, backend `/health.php`), cada 60s.
- Canal de notificación por email.
- 4 alertas: caída del front, caída del back (disponibilidad, SLA 99,5%), lentitud del front/back >3000ms (tiempo de respuesta comprometido).

Pendiente: `terraform apply` real — bloqueado por falta de `terraform.tfvars` de `Fase2` (15 variables, incluida la contraseña de la BD), pedido a Ana junto con las credenciales de GitHub de OPS-001. Alertas Slack/Teams no incluidas todavía (depende de OPS-006, canal de comunicación, aún sin crear).
