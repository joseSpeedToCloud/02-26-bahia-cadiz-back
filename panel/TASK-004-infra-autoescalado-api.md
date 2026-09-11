# TASK-004 — Autoescalado y arquitectura API-first en Cloud Run/GKE

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-145 (SPEC-001), relacionado con REQ-011 y REQ-058 (ver [`TASK-029`](TASK-029-infra-dimensionamiento-escalabilidad.md))
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, terraform, gcp, api
**Estado:** Hecho

## Descripción técnica

Configurar autoescalado horizontal (min/max instancias) del backend/API del CMS y del portal, cumpliendo el requisito de arquitectura basada en microservicios o API-first con autoescalado en entornos cloud.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform`**:

- `Fase2/2.Cloud-Run-Back/main.tf`: `scaling { min_instance_count / max_instance_count }`, con los valores fijados en la llamada al módulo en `Fase2/main.tf:57-58` → **`min_instances = 1`, `max_instances = 3`** (corregido: antes este documento decía por error `min=0, max=10` también para el back).
- `Fase2/1.Cloud-Run-Front/main.tf`: mismo mecanismo `scaling{}`, sin sobrescribir en `Fase2/main.tf` → se aplican los defaults del módulo, **`min_instances = 0`, `max_instances = 10`**.
- Arquitectura API-first ya confirmada en la práctica: el CMS (Drupal) expone JSON:API y el portal (Angular/Ionic) consume esa API — sin acoplamiento de renderizado servidor a servidor.

**Prueba de carga básica ejecutada** (REQ-145): 30 workers concurrentes (15 front + 15 back) durante 60s contra las URLs públicas reales.

- Front: 25.249 peticiones OK, 0 fallos.
- Back (Drupal, bootstrap completo): 625 peticiones OK, 0 fallos.
- Cloud Monitoring confirma escalado real de 0 → 1 instancia activa en ambos servicios durante la ventana de la prueba (partiendo de reposo, `min_instances=0`), sin caídas ni errores.

**Prueba de carga repetida con artefacto reproducible** (antes solo quedaba como texto en este panel/el issue, sin script versionado): `02-26-infra-terraform/scripts/load-test.py` (stdlib de Python, sin dependencias) — mismos parámetros (30 workers, 60s por servicio). Resultado de la ejecución real, guardado en `02-26-infra-terraform/evidence/load-test-20260729-113011.json`:

- Front: 6.776 peticiones OK, 0 fallos (latencia máx. 3.115 ms — pico de arranque en frío coherente con `min_instances=0`).
- Back: 7.941 peticiones OK, 0 fallos (latencia máx. 842 ms).
