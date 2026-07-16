# TASK-009 — Pipeline CI/CD (GitHub Actions) con despliegue a preproducción y producción

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-150 (SPEC-001)
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, ci-cd, github-actions
**Estado:** Pendiente

## Descripción técnica

Configurar pipeline de integración continua que ejecute `terraform plan`/`apply` y despliegue del CMS/portal a preproducción tras cada PR a `main`, y a producción tras aprobación manual, conforme a las Fases 3 y 4 del PPT.

## Entregable / Evidencia

Workflows `.github/workflows/` configurados y documentados.
