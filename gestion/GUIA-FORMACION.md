# Guía de formación técnica y administrativa

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-015 (apartado "formación de personal técnico y administrativo")
**Fecha:** 2026-07-30
**Estado:** Material de formación redactado a partir de la implementación real. La sesión de formación en sí (con personal real de la Mancomunidad) sigue pendiente de programar — ver apartado 3.

---

## 1. Formación de personal administrativo (editores de contenido)

Dirigida a las personas que van a mantener el contenido del portal (agenda, playas, rutas, transporte, aparcamientos, avisos, meteorología).

**Contenido de la sesión:**
1. Acceso al CMS (Drupal) — login, recuperación de contraseña.
2. Alcance de permisos: cada editor municipal solo ve/edita el contenido de su propio municipio (multisitio — ver `web/modules/custom/bahia_multisitio`).
3. Crear/editar/despublicar contenido por cada uno de los 7 tipos — ver el detalle de campos en [`gestion/MANUAL-USO-WEB.md`](MANUAL-USO-WEB.md), apartado 2.
4. Buenas prácticas: imágenes optimizadas (peso/tamaño), coordenadas GPS correctas (afectan directamente al mapa del portal), no dejar fechas de fin vacías en avisos de tráfico cuando se conozcan.
5. Cómo verificar que un cambio se refleja en el portal público (recarga con pull-to-refresh, no requiere ningún paso de "publicación" adicional).

**Material de apoyo**: el propio manual de uso ([`gestion/MANUAL-USO-WEB.md`](MANUAL-USO-WEB.md)) sirve como referencia post-formación.

## 2. Formación de personal técnico

Dirigida al personal que vaya a operar o dar soporte a la infraestructura tras la entrega (no necesariamente el mismo equipo que la desarrolló).

**Contenido de la sesión:**

1. **Estructura del proyecto**: 3 repositorios (`02-26-infra-terraform`, `02-26-web-back`, `02-26-web-front`) + el panel de gestión (`02-26-bahia-cadiz-back`) con la trazabilidad REQ/OPS → SPEC → TASK (ver [`gestion/METODOLOGIA-DESARROLLO.md`](METODOLOGIA-DESARROLLO.md)).
2. **Infraestructura (Terraform)**: fases 0-3 (`02-26-infra-terraform/README.md`), cómo aplicar cambios (`terraform plan`/`apply` por fase), dónde está el estado remoto (bucket GCS con locking).
3. **Despliegue**: Cloud Build como motor de build/deploy (`cloudbuild.yaml` en cada repo de código), disparado por push a la rama correspondiente.
4. **Arquitectura de datos**: CMS Drupal (JSON:API de solo lectura) como repositorio único de contenidos, consumido por el portal — ver [`gestion/ARQUITECTURA-INTEGRACION.md`](ARQUITECTURA-INTEGRACION.md).
5. **Monitorización y alertas**: dashboard de Cloud Monitoring (SLA, latencia, instancias activas) y canal de notificación por email configurado — `02-26-infra-terraform/Fase2/4.Monitoring/`.
6. **Backups y restauración**: backups diarios automáticos de Cloud SQL (retención 30 días), procedimiento de clonado para restauración de emergencia — ver evidencia en REQ-146.
7. **Seguridad**: IAM de mínimo privilegio, Organization Policies aplicadas, y el hueco pendiente del marco organizativo ENS — ver [`cumplimiento/INFORME-ENS-BASICO.md`](../cumplimiento/INFORME-ENS-BASICO.md).
8. **Aprovisionamiento de contenidos**: scripts idempotentes en `02-26-web-back/scripts/` (`provision-content-types.php`, `provision-multisitio.php`) para replicar el modelo de contenidos en un entorno nuevo.

## 3. Pendiente

Este documento es el **material** de formación, redactado y verificable contra la implementación real. La **impartición real** de ambas sesiones (con personal designado de la Mancomunidad, en fecha acordada) no se ha podido realizar en este trabajo — requiere que la Mancomunidad identifique y convoque a las personas concretas que van a asumir cada rol, algo pendiente de coordinación organizativa (mismo bloqueo ya señalado en [`gestion/METODOLOGIA-DESARROLLO.md`](METODOLOGIA-DESARROLLO.md), apartado 6, y para la coordinación con ayuntamientos en REQ-013).

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
