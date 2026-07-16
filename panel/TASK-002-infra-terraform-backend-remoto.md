# TASK-002 — Backend remoto de Terraform (GCS + locking)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-143 (SPEC-001), relacionado con REQ-093
**Prioridad:** Highest
**Story Points:** 3
**Labels:** infra, terraform, gcp
**Estado:** Pendiente

## Descripción técnica

Configurar bucket de Cloud Storage con versionado como backend remoto del estado de Terraform, con locking para evitar condiciones de carrera entre despliegues.

## Entregable / Evidencia

Backend configurado en `environments/*/backend.tf` + prueba de apply concurrente controlada.
