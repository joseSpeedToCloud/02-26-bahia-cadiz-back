# TASK-004 — Autoescalado y arquitectura API-first en Cloud Run/GKE

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-145 (SPEC-001), relacionado con REQ-011
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, terraform, gcp, api
**Estado:** Hecho

## Descripción técnica

Configurar autoescalado horizontal (min/max instancias) del backend/API del CMS y del portal, cumpliendo el requisito de arquitectura basada en microservicios o API-first con autoescalado en entornos cloud.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform`**:
- `Fase2/1.Cloud-Run-Front/main.tf` y `Fase2/2.Cloud-Run-Back/main.tf`: `min_instances = 0`, `max_instances = 10` (configurable por variable), aplicado como `scaling { min_instance_count / max_instance_count }`.
- Arquitectura API-first ya confirmada en la práctica: el CMS (Drupal) expone JSON:API y el portal (Angular/Ionic) consume esa API — sin acoplamiento de renderizado servidor a servidor.

Pendiente: la "prueba de carga básica" explícita del entregable original — no se ha ejecutado ningún test de carga real todavía (relacionado con el INT-005 del proyecto de Poniente Granada, mismo tipo de hueco).
