# TASK-009 — Pipeline CI/CD (GitHub Actions) con despliegue a preproducción y producción

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-150 (SPEC-001)
**Prioridad:** High
**Story Points:** 5
**Labels:** infra, ci-cd, github-actions
**Estado:** Hecho (con matiz — no es GitHub Actions)

## Descripción técnica

Configurar pipeline de integración continua que ejecute `terraform plan`/`apply` y despliegue del CMS/portal a preproducción tras cada PR a `main`, y a producción tras aprobación manual, conforme a las Fases 3 y 4 del PPT.

## Entregable / Evidencia

**El CI/CD del CMS/portal está implementado y funcionando de verdad** (lo hemos visto desplegar en vivo varias veces en esta sesión), pero con **Cloud Build**, no GitHub Actions:
- `02-26-infra-terraform/Fase1` crea los triggers de Cloud Build (`gcp-cbt-front-02-26-bh`, `gcp-cbt-back-02-26-bh`) conectados a los repos `02-26-web-front`/`02-26-web-back` vía GitHub App.
- Cada push a `main` reconstruye la imagen y redespliega a Cloud Run automáticamente (`cloudbuild.yaml` en cada repo de aplicación).

No hay despliegue a preproducción ni aprobación manual — no existe ese entorno todavía (ver TASK-003, sigue pendiente). Cloud Build cumple el objetivo funcional (CI/CD automático), pero no coincide literalmente con "GitHub Actions" ni con el flujo de dos entornos que pedía el entregable original — vale la pena comentarlo con Ana/Arnaldo para decidir si se documenta como cumplido así o se exige migrar a GitHub Actions.
