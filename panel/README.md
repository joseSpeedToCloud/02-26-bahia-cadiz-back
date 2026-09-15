# Panel de Tasks — Backend (Plataforma Digital Bahía de Cádiz)

Deriva de [`especificaciones/SPEC-001-plataforma-bahia-cadiz.md`](../especificaciones/SPEC-001-plataforma-bahia-cadiz.md). Cubre solo la parte de **backend**: infraestructura cloud y núcleo del CMS. Total: 38 tasks, 123 story points.

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

## CMS (Drupal headless) — 87 SP *(story points estimados; el checklist original no detallaba esta categoría en esta sesión)*

| ID | Título | Prioridad | SP | Estado |
|----|--------|-----------|----|--------|
| [TASK-010](TASK-010-backend-cms-headless-base.md) | Selección y puesta a punto base del CMS (Drupal) | Highest | 5 | Hecho |
| [TASK-011](TASK-011-backend-arquitectura-headless-api.md) | Arquitectura headless / API estructurada y versionada | Highest | 8 | **Hecho** |
| [TASK-012](TASK-012-backend-integracion-portal.md) | CMS como backend unificado / repositorio de contenidos | High | 5 | **Hecho** |
| [TASK-013](TASK-013-backend-puesta-produccion-carga-contenidos.md) | Puesta en producción + carga de contenidos existentes | High | 5 | **Hecho** |
| [TASK-014](TASK-014-backend-admin-usuarios-permisos.md) | Administración simplificada + permisos por rol | High | 5 | **En progreso** (solo falta prueba con usuario no técnico) |
| [TASK-015](TASK-015-backend-tipos-contenido-workflows.md) | Tipos de contenido + flujos editoriales + versionado | Highest | 8 | **Hecho** |
| [TASK-016](TASK-016-backend-multiidioma.md) | Gestión multilingüe con traducción integrada | High | 5 | **Hecho** |
| [TASK-017](TASK-017-backend-multisitio.md) | Soporte multisitio para los municipios | Medium | 5 | **Hecho** |
| [TASK-018](TASK-018-backend-taxonomias-navegacion.md) | Taxonomías, categorías jerárquicas y navegación | High | 3 | **Hecho** |
| [TASK-019](TASK-019-backend-gestion-archivos-integraciones.md) | Gestión de archivos e integraciones (SEO/agenda/redes/semántica) | Medium | 5 | **Hecho** |
| [TASK-020](TASK-020-backend-contenido-multimedia.md) | Contenido multimedia en fichas turísticas (audio, vídeo, foto 360°, valoración) | High | 5 | **Hecho** |
| [TASK-021](TASK-021-backend-geolocalizacion-mapas.md) | Geolocalización de todos los recursos en el mapa (rutas GPX + avisos de tráfico) | Medium | 3 | **Hecho** |
| [TASK-022](TASK-022-backend-copias-seguridad-cms.md) | Copias de seguridad sencillas desde el CMS (contenido, ficheros y BD) | Medium | 3 | **Hecho** |
| [TASK-023](TASK-023-backend-grafo-conocimiento-une178503.md) | Grafo de conocimiento / interoperabilidad semántica (schema.org, UNE 178503) | Highest | 5 | **Hecho** |
| [TASK-024](TASK-024-backend-metadatos-obligatorios-validacion-ontologica.md) | Metadatos semánticos obligatorios + validación ontológica antes de producción | Highest | 5 | **Hecho** |
| [TASK-025](TASK-025-backend-geolocalizacion-eventos-mapa.md) | Acceso a los recursos correctamente geolocalizados en el mapa interactivo (eventos) | Medium | 2 | **Hecho** |
| [TASK-026](TASK-026-solucion-llave-en-mano-produccion.md) | Solución llave en mano: portal + sistemas de operatividad + integración, verificado en producción | Highest | 3 | **Hecho** |
| [TASK-027](TASK-027-navegacion-acceso-contenido-recursos.md) | Navegación y acceso claro/ordenado/geoposicionado al contenido (corrige hueco real de audio/vídeo/valoración) | Media | 3 | **Hecho** |
| [TASK-028](TASK-028-cuentas-publicas-portal.md) | Cuentas públicas del portal: registro, preferencias, encuestas + resultados, valoraciones, borrado de cuenta (RGPD) | Media | 9 | **Hecho** |
| [TASK-029](TASK-029-infra-dimensionamiento-escalabilidad.md) | Dimensionamiento de arquitectura para escalabilidad (REQ-058) | Media | 5 | En progreso |
| [TASK-030](TASK-030-acceso-anonimo-registro-voluntario.md) | Acceso sin autenticación por defecto, registro voluntario (REQ-059) | Media | 1 | En progreso |
| [TASK-031](TASK-031-compartir-redes-sociales.md) | Compartir en redes sociales (REQ-061) | Media | 1 | En progreso |
| [TASK-032](TASK-032-seguridad-conexiones-permisos-minimos.md) | Seguridad: conexiones seguras y permisos mínimos (REQ-062) | Alta | 2 | En progreso |
| [TASK-033](TASK-033-framework-moderno-pwa-capacitor.md) | Framework moderno, PWA offline y contenedores nativos (REQ-063) | Media | 1 | En progreso |
| [TASK-034](TASK-034-empaquetado-hibrido-android-ios.md) | Empaquetado híbrido Android/iOS sin reescritura (REQ-064) | Media | 1 | En progreso |
| [TASK-035](TASK-035-framework-typescript-spa-di.md) | TypeScript, SPA con lazy loading, change detection y DI (REQ-065) | Media | 1 | En progreso |
| [TASK-036](TASK-036-pwa-offline-datos-push.md) | PWA: offline de datos y notificaciones push (REQ-066) | Media | 5 | En progreso |
| [TASK-037](TASK-037-framework-ci-testing.md) | Framework recomendado, CI y pruebas automatizadas (REQ-067) | Media | 3 | En progreso |
| [TASK-038](TASK-038-agenda-eventos.md) | Módulo de Agenda de Eventos (REQ-069) | Alta | 3 | **Hecho** (con matiz — ver "Vista de agenda") |

## Fuera de alcance de este panel (por ahora)

Frontend (Portal, app móvil, UI de Agenda/Playas/Movilidad) — tiene panel propio desde 2026-08-07: [`panel/README-frontend.md`](README-frontend.md). El resto de categorías del pliego (Integración, Contractual/Administrativo, Gestión, Gestión Personal) siguen sin panel — se planificarán en paneles/specs separados.

## Orden recomendado

1. **TASK-007** (certificación energética) antes o en paralelo a TASK-001/003 — condiciona la región de GCP.
2. **TASK-001 → TASK-002 → TASK-003** — base de Terraform.
3. **TASK-010 → TASK-011** — base del CMS y arquitectura headless, antes del resto de tasks de CMS.
4. **TASK-008** (IAM/ENS/RGPD) y **TASK-009** (CI/CD) en paralelo a la construcción del CMS.
5. **TASK-004, TASK-005, TASK-006** — autoescalado, backups y monitorización, sobre la infraestructura ya desplegada.
6. **TASK-012 → TASK-013 → TASK-014 → TASK-015 → TASK-016 → TASK-017 → TASK-018 → TASK-019 → TASK-020 → TASK-021 → TASK-025** — resto de funcionalidad del CMS.
