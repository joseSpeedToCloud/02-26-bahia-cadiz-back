# TASK-006 — Monitorización y alertas de disponibilidad (SLA 99,5%)

**Categoría:** Soporte/Garantía
**Requisito origen:** REQ-147 (SPEC-001), relacionado con REQ-086 y REQ-127
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, monitoring, sla
**Estado:** Hecho

## Descripción técnica

Configurar Cloud Monitoring con uptime checks y alertas (email/Slack) para el cumplimiento del SLA de disponibilidad comprometido en la oferta y del tiempo de carga máximo de 3 segundos.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform/Fase2/4.Monitoring`** (módulo nuevo, enganchado en `Fase2/main.tf`):

- 2 uptime checks (portal front `/`, backend `/health.php`), cada 60s.
- Canal de notificación por email.
- 4 alertas: caída del front, caída del back (disponibilidad, SLA 99,5%, basadas en `check_passed`), lentitud del front/back p95 >3000ms (basadas en la métrica nativa `run.googleapis.com/request_latencies`, más fiable que `check_latency` del uptime check, que tardó horas en registrar datos en pruebas reales).
- `terraform apply` ejecutado en real — todos los recursos creados y verificados. Canal de email verificado (`jose.sanchez@speedtocloud.com`).

Pendiente (no bloqueante): alertas Slack/Teams (depende de OPS-006, canal de comunicación, aún sin crear).
