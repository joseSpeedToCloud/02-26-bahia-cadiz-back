# Índice de entregables contractuales

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** OPS-005 (apartado "entregables contractuales")
**Fecha:** 2026-08-03
**Nota:** Este índice enlaza a la evidencia real de cada entregable, no la duplica. Se actualiza según se cierran issues del pliego. Todo lo listado aquí está **pendiente de validación por el responsable del contrato (Mancomunidad)** salvo que se indique lo contrario — ver cada enlace para el detalle.

---

## Infraestructura (`02-26-infra-terraform`)

| Entregable | Evidencia |
|---|---|
| Backend remoto de Terraform (GCS + locking) | `panel/TASK-002-infra-terraform-backend-remoto.md` |
| Autoescalado + arquitectura API-first | `panel/TASK-004-infra-autoescalado-api.md`, prueba de carga real en `02-26-infra-terraform/evidence/` |
| Backups diarios + prueba de restauración completa | `panel/TASK-005-infra-backups-diarios.md` |
| Monitorización y alertas SLA 99,5% + dashboard | `panel/TASK-006-infra-monitorizacion-sla.md` |
| CI/CD (vía Cloud Build) | `panel/TASK-009-infra-cicd-github-actions.md` |
| Organization Policies (IAM, claves, buckets) | `02-26-infra-terraform/Fase0/org_policies.tf` |

## CMS / Backend (`02-26-web-back`)

| Entregable | Evidencia |
|---|---|
| CMS Drupal en producción | `panel/TASK-010-backend-cms-headless-base.md` |
| CMS como backend unificado (JSON:API, arquitectura de integración) | `gestion/ARQUITECTURA-INTEGRACION.md`, `panel/TASK-012-backend-integracion-portal.md` |
| Puesta en producción + carga de contenidos legacy existentes | `gestion/INFORME-MIGRACION-CONTENIDOS.md`, `panel/TASK-013-backend-puesta-produccion-carga-contenidos.md` |
| Gestión multilingüe (es/en) | `panel/TASK-016-backend-multiidioma.md` |
| Soporte multisitio por municipio | `panel/TASK-017-backend-multisitio.md` |
| Mapeo semántico UNE 178503 | `gestion/MAPEO-UNE-178503.md` |

## Portal / Frontend (`02-26-web-front`)

| Entregable | Evidencia |
|---|---|
| Scaffold Ionic/Angular, capa JSON:API, páginas (Inicio, Agenda, Playas, Movilidad, Mapa) | `panel/TASK-001` a `TASK-007` en `02-26-web-front/panel/` |
| Empaquetado Android/iOS (Capacitor) | Verificado con build/instalación/lanzamiento real (REQ-006) |
| Auditoría de accesibilidad (Lighthouse) | `02-26-web-front/evidence/INFORME-ACCESIBILIDAD.md`, `panel/TASK-008-accesibilidad-wcag.md` (parcial, ver informe) |

## Gestión

| Entregable | Evidencia |
|---|---|
| Metodología de desarrollo e interacción con la Mancomunidad | `gestion/METODOLOGIA-DESARROLLO.md` |
| Modelo de gobierno (roles, SLA) | `gestion/MODELO-GOBIERNO.md` |
| Manual de uso de la web | `gestion/MANUAL-USO-WEB.md` |
| Guía de formación técnica y administrativa | `gestion/GUIA-FORMACION.md` |
| Pruebas técnicas de rendimiento y seguridad | `gestion/PRUEBAS-TECNICAS-SEGURIDAD-RENDIMIENTO.md` |

## Cumplimiento (ENS / seguridad)

| Entregable | Evidencia |
|---|---|
| Informe de cumplimiento ENS categoría básica | `cumplimiento/INFORME-ENS-BASICO.md` (marco técnico completo; marco organizativo formal pendiente — ver el propio informe) |

---

Este índice no sustituye la trazabilidad issue-a-issue de `panel/README.md` — es una vista agrupada por tipo de entregable para consulta rápida.
