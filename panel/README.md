# Panel de Tasks — Backend (Plataforma Digital Bahía de Cádiz)

Deriva de [`especificaciones/SPEC-001-plataforma-bahia-cadiz.md`](../especificaciones/SPEC-001-plataforma-bahia-cadiz.md). Cubre solo la parte de **backend**: infraestructura cloud y núcleo del CMS. Total: 19 tasks, 62 story points.

## Infraestructura / Cloud (GCP + Terraform + CI/CD) — 44 SP

| ID | Título | Prioridad | SP | Estado |
|----|--------|-----------|----|--------|
| [TASK-001](TASK-001-infra-terraform-modulos.md) | Estructura de módulos Terraform | Highest | 8 | Pendiente |
| [TASK-002](TASK-002-infra-terraform-backend-remoto.md) | Backend remoto de Terraform (GCS + locking) | Highest | 3 | **Hecho** |
| [TASK-003](TASK-003-infra-entornos-separados.md) | Entornos separados (dev/preprod/prod) | High | 5 | Pendiente |
| [TASK-004](TASK-004-infra-autoescalado-api.md) | Autoescalado y arquitectura API-first | High | 5 | **Hecho** |
| [TASK-005](TASK-005-infra-backups-diarios.md) | Backups diarios, retención 30 días | High | 3 | **Hecho** |
| [TASK-006](TASK-006-infra-monitorizacion-sla.md) | Monitorización y alertas SLA 99,5% | High | 5 | **Hecho** |
| [TASK-007](TASK-007-infra-certificacion-energetica.md) | Certificación energética del datacenter (condición especial B) | Highest | 2 | Pendiente |
| [TASK-008](TASK-008-seguridad-iam-ens-rgpd.md) | Políticas IAM y cifrado (ENS básico + RGPD) | Highest | 8 | **En progreso** |
| [TASK-009](TASK-009-infra-cicd-github-actions.md) | Pipeline CI/CD (GitHub Actions) | High | 5 | **Hecho** (vía Cloud Build, ver nota en la task) |

> Reconciliado el 2026-07-20 contra el Terraform real (`02-26-infra-terraform`), que no se conocía cuando se creó este panel — ver cada task para el detalle de la evidencia encontrada.

## CMS (Drupal headless) — 49 SP *(story points estimados; el checklist original no detallaba esta categoría en esta sesión)*

| ID | Título | Prioridad | SP | Estado |
|----|--------|-----------|----|--------|
| [TASK-010](TASK-010-backend-cms-headless-base.md) | Selección y puesta a punto base del CMS (Drupal) | Highest | 5 | Hecho |
| [TASK-011](TASK-011-backend-arquitectura-headless-api.md) | Arquitectura headless / API estructurada y versionada | Highest | 8 | En progreso |
| [TASK-012](TASK-012-backend-integracion-portal.md) | CMS como backend unificado / repositorio de contenidos | High | 5 | **Hecho** |
| [TASK-013](TASK-013-backend-puesta-produccion-carga-contenidos.md) | Puesta en producción + carga de contenidos existentes | High | 5 | Pendiente |
| [TASK-014](TASK-014-backend-admin-usuarios-permisos.md) | Administración simplificada + permisos por rol | High | 5 | Pendiente |
| [TASK-015](TASK-015-backend-tipos-contenido-workflows.md) | Tipos de contenido + flujos editoriales + versionado | Highest | 8 | En progreso |
| [TASK-016](TASK-016-backend-multiidioma.md) | Gestión multilingüe con traducción integrada | High | 5 | Pendiente |
| [TASK-017](TASK-017-backend-multisitio.md) | Soporte multisitio para los municipios | Medium | 5 | En progreso |
| [TASK-018](TASK-018-backend-taxonomias-navegacion.md) | Taxonomías, categorías jerárquicas y navegación | High | 3 | En progreso |
| [TASK-019](TASK-019-backend-gestion-archivos-integraciones.md) | Gestión de archivos e integraciones (SEO/agenda/redes/semántica) | Medium | 5 | Pendiente |

## Fuera de alcance de este panel (por ahora)

Frontend (Portal, app móvil, UI de Agenda/Playas/Movilidad) y el resto de categorías del pliego (Diseño/UX, Integración, Contractual/Administrativo, Gestión, Gestión Personal) — se planificarán en paneles/specs separados.

## Orden recomendado

1. **TASK-007** (certificación energética) antes o en paralelo a TASK-001/003 — condiciona la región de GCP.
2. **TASK-001 → TASK-002 → TASK-003** — base de Terraform.
3. **TASK-010 → TASK-011** — base del CMS y arquitectura headless, antes del resto de tasks de CMS.
4. **TASK-008** (IAM/ENS/RGPD) y **TASK-009** (CI/CD) en paralelo a la construcción del CMS.
5. **TASK-004, TASK-005, TASK-006** — autoescalado, backups y monitorización, sobre la infraestructura ya desplegada.
6. **TASK-012 → TASK-013 → TASK-014 → TASK-015 → TASK-016 → TASK-017 → TASK-018 → TASK-019** — resto de funcionalidad del CMS.
