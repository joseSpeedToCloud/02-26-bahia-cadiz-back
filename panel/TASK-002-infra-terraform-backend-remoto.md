# TASK-002 — Backend remoto de Terraform (GCS + locking)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-143 (SPEC-001), relacionado con REQ-093
**Prioridad:** Highest
**Story Points:** 3
**Labels:** infra, terraform, gcp
**Estado:** Hecho

## Descripción técnica

Configurar bucket de Cloud Storage con versionado como backend remoto del estado de Terraform, con locking para evitar condiciones de carrera entre despliegues.

## Entregable / Evidencia

**Implementado en el repo `02-26-infra-terraform`** (no en este repo, es infraestructura aparte gestionada por Ana/Arnaldo):
- `bucket-tfstate/bucket-tfstate.tf` — bucket GCS dedicado (`gcp-sbck-tfs-02-26-bh`) con versionado, usado como backend remoto.
- Cada fase (`Fase0/backend.tf`, `Fase1/backend.tf`, `Fase2/backend.tf`, `Fase3/backend.tf`) apunta a ese mismo bucket con un `prefix` propio (`fase0`, `fase1`, etc.) — locking nativo de GCS (generation precondition), sin necesidad de DynamoDB ni recurso adicional.

No está en `environments/*/backend.tf` como sugería originalmente esta task (esa convención de carpetas no se usó, ver TASK-001/TASK-003), pero el objetivo real — estado remoto + locking — está cumplido.
