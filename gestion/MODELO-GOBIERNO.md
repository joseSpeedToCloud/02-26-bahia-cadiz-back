# Modelo de gobierno del proyecto

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** OPS-004
**Fecha:** 2026-08-03
**Estado:** Documenta lo verificable/real hoy. El calendario de comisiones con los Ayuntamientos y los interlocutores del contrato son decisiones de la Mancomunidad, no de SpeedToCloud — quedan como pendiente explícito (apartado 4).

---

## 1. Roles — equipo SpeedToCloud

| Persona | Rol IAM actual en GCP (`id-2190759668-26`) | Nota |
|---|---|---|
| Arnaldo Morales | `roles/owner` | — |
| Ana Tormo Domínguez | `roles/owner` | ⚠️ Ver hallazgo abajo |
| Jose Sánchez | `roles/owner` | ⚠️ Ver hallazgo abajo |

**Hallazgo real (2026-08-03)**: verificado con `gcloud projects get-iam-policy`, los 3 miembros del equipo tienen actualmente `roles/owner` sobre el proyecto. Esto contradice lo declarado previamente en `cumplimiento/INFORME-ENS-BASICO.md` ("un único Owner, resto con roles más acotados") — ese informe ya se ha corregido para reflejar el estado real. Queda pendiente que el equipo decida a quién quitar el rol Owner y qué rol más acotado asignar en su lugar (no se ha tocado el IAM real en este trabajo, a la espera de esa decisión).

## 2. Rol pendiente de designar por el cliente

**Responsable del contrato** (LCSP art. 62): debe ser designado por la Mancomunidad, no puede ser personal de SpeedToCloud. Este rol es el que valida el cierre de prácticamente todos los issues de este proyecto — su ausencia es el hallazgo más repetido de toda la auditoría de esta sesión.

## 3. SLA — ya comprometidos técnicamente

| SLA | Valor comprometido | Estado |
|---|---|---|
| Disponibilidad | 99,5% | Implementado y monitorizado (uptime checks + alertas, `02-26-infra-terraform/Fase2/4.Monitoring`) |
| Tiempo de respuesta | Latencia p95 bajo umbral (`var.latency_threshold_ms`) | Implementado y monitorizado, verificado con prueba de carga real (REQ-145) |
| Backups | Diarios, retención 30 días | Implementado y verificado con prueba de restauración real (REQ-146) |

Estos SLA ya son reales y auditables — no son una propuesta, son lo que ya está desplegado y monitorizado.

## 4. Pendiente — decisión de la Mancomunidad, no resoluble aquí

- **Calendario de comisiones con los Ayuntamientos**: requiere que la Mancomunidad convoque y fije una periodicidad real (mensual, trimestral, etc.) con los 6 municipios miembros.
- **Interlocutores del contrato**: la Mancomunidad debe designar personas concretas — de su lado y confirmar los de SpeedToCloud — como puntos de contacto formales.
- **Responsable del contrato**: ver apartado 2.

No se han inventado nombres, fechas ni cadencias para estos tres puntos: sería fabricar un dato de gobernanza sin validez real.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
