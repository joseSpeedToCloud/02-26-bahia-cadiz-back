# TASK-029 — Dimensionamiento de arquitectura para escalabilidad (REQ-058)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-058 (PPT ap. 4.2), relacionado con TASK-004 (REQ-145)
**Prioridad:** Media
**Story Points:** 5
**Labels:** infra, terraform, gcp, escalabilidad, cloud-sql, load-balancer
**Estado:** En progreso — auditoría realizada, decisiones de coste pendientes

## Descripción técnica

Diseño para escalar ante aumento de carga de usuarios/información mediante dimensionamiento adecuado de la arquitectura hardware.

## Auditoría realizada (contra el Terraform real de `02-26-infra-terraform`, no documentación)

### Cumple

- **Cloud Run back y front**: autoescalado horizontal real vía `scaling{min_instance_count/max_instance_count}`. Back: `min=1, max=3` (`Fase2/main.tf:57-58`). Front: `min=0, max=10` (defaults del módulo, `Fase2/1.Cloud-Run-Front/main.tf:19-20`).
- **Prueba de carga real documentada** en TASK-004: escalado 0→1 observado en Cloud Monitoring, 0 fallos (front y back).
- **Backups de Cloud SQL**: configurados (`point_in_time_recovery`, retención 30 backups) — resiliencia, no escalado, pero correcto.
- **Lifecycle de almacenamiento** en el bucket de ficheros de Drupal (borrado/archivado automático por antigüedad) — gestión de coste, no escalado de cómputo.

### No cumple / hallazgos (dejados sin tocar, pendientes de decisión — ver más abajo)

1. **Cloud SQL en `db-f1-micro`** (el tier más pequeño de GCP), **sin alta disponibilidad** (`availability_type = "ZONAL"`, sin failover) **ni réplicas de lectura**, y **sin `disk_autoresize`/`disk_size` explícito** en Terraform (`Fase2/3.CloudSQL/main.tf:55-78`). El propio `README.md` del repo lo reconoce: *"subir antes de una carga de producción real"* (`README.md:86`).
2. **No hay Load Balancer ni CDN aplicados.** El código del LB (`Fase3/main.tf`) existe pero **nunca se ha ejecutado `terraform apply`** (sin `.terraform/`/state, a diferencia de `Fase0`/`Fase2`). Todo el tráfico va directo a las URLs `*.run.app`. Aun aplicado, ese LB solo cubriría el front, no el back/CMS (comentario explícito en `Fase3/main.tf:9`).
3. **Concurrencia por instancia de Cloud Run no configurada explícitamente** en ningún servicio (queda en el default de la API, 80).
4. **La carpeta `environments/` (dev/preprod/prod)** con parámetros de escalado por entorno es código muerto: no está conectada a ningún recurso real, y usa una región distinta (`europe-west1`) a la real del proyecto (`europe-southwest1`). Coincide con `TASK-003` (Entornos separados), que ya está marcada **Pendiente** — no es una regresión nueva, es el mismo hallazgo visto desde otro ángulo.

## Decisión (2026-09-10)

Los hallazgos 1 y 2 implican coste económico mensual adicional en el proyecto GCP real (subir tier de Cloud SQL, aplicar Load Balancer). Se decide **documentarlos y dejarlos pendientes de decisión**, sin aplicar el cambio todavía.

## Pendiente antes de marcar como Hecho

- [ ] Decidir si se sube el tier de Cloud SQL (y a cuál) antes de producción real
- [ ] Decidir si se aplica `Fase3` (Load Balancer) — requiere dominio propio definitivo
- [ ] Configurar `concurrency` explícito en Cloud Run si el rendimiento real lo requiere
- [ ] Resolver o eliminar la carpeta `environments/` (código muerto) — coordinar con TASK-003
- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #79
