# Metodología de desarrollo e interacción con la Mancomunidad

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-012 (Checklist_PlataformaDigitalBahiaCadiz.xlsx, apartado 3 del pliego)
**Fecha:** 2026-07-30
**Estado:** Refleja la metodología realmente en uso desde el inicio del proyecto (no es un plan teórico redactado a posteriori) — ver apartado 5 para la evidencia de cada práctica.

---

## 1. Planificación del proyecto

El proyecto se organiza en dos niveles de documentación, versionados en Git junto con el código:

- **Especificaciones (`especificaciones/SPEC-XXX-*.md`)**: nivel alto — qué se construye y por qué. Cada SPEC agrupa un conjunto de tareas relacionadas (p. ej. `SPEC-001-plataforma-bahia-cadiz.md` cubre toda la plataforma).
- **Tasks (`panel/TASK-XXX-*.md`)**: una por entregable concreto, con categoría, requisito origen (REQ/OPS del pliego), prioridad, story points, estado y evidencia de entrega.

Cada elemento del pliego/checklist tiene un ID trazable único (REQ-XXX / OPS-XXX) que se conserva sin cambios desde el Excel original hasta el issue de GitHub y, cuando aplica, hasta la task del panel — es la clave que permite auditar en cualquier momento que lo entregado corresponde a lo pedido.

## 2. Calendario de hitos

Los hitos se organizan por fases de entrega, reflejadas directamente en la estructura del repositorio de infraestructura (`02-26-infra-terraform`):

| Fase | Contenido | Estado |
|---|---|---|
| Fase 0 | Fundacional: proyecto GCP, IAM, Organization Policies, bucket de estado remoto de Terraform | Aplicada |
| Fase 1 | Análisis y definición: alta de repositorios, entornos, backend remoto de Terraform | Aplicada |
| Fase 2 | Despliegue del runtime cloud: Cloud Run (front/back), Cloud SQL, monitorización/alertas, autoescalado | Aplicada y verificada con prueba de carga real |
| Fase 3 | Load Balancer + dominio propio | Pendiente — condicionada a que la Mancomunidad decida el dominio definitivo |

Cada issue de GitHub declara además un **milestone propuesto** (p. ej. "Fase 1 — Análisis y definición") como referencia de calendario dentro de esta misma estructura de fases, de forma que el hito de cada requisito del pliego queda ligado a una fase de infraestructura concreta y verificable, no a una fecha aislada sin contexto técnico.

## 3. Organización de los trabajos

- Cada task del panel indica su **prioridad** (Highest/High/Medium), sus **story points** y su **estado** (Pendiente → En progreso → Hecho).
- `panel/README.md` mantiene, por cada bloque funcional (infraestructura, CMS, etc.), una tabla-índice con el estado agregado y una sección explícita de **"Orden recomendado"** que documenta las dependencias reales entre tasks (p. ej. TASK-007 condiciona la región de GCP antes de TASK-001/003; TASK-010→TASK-011 antes del resto de tasks de CMS).
- El estado del panel se **reconcilia periódicamente contra el estado real** de la infraestructura y el código (no se asume que lo declarado en un momento dado siga siendo cierto más adelante) — ver por ejemplo el commit `panel de infra contra 02-26-infra-terraform: TASK-002/004/005/009 hechas, TASK-008 parcial` (2026-07-20), donde se corrigió el panel tras auditar el Terraform real.

## 4. Herramientas de seguimiento y control

- **GitHub Issues** (uno por cada REQ/OPS del pliego) como tablero de seguimiento cara al cliente, con columnas Pendiente/En progreso/Hecho y comentarios de evidencia en cada cierre.
- **`panel/README.md` y los `TASK-XXX-*.md`** como índice técnico interno, versionado en Git junto con el código al que se refiere.
- **Historial de Git** (`git log`) como registro de auditoría inmutable de cuándo y cómo se implementó cada entregable — usado sistemáticamente en este proyecto para verificar que un issue marcado "Hecho" corresponde a cambios reales aplicados, no solo declarados.
- **Criterio de cierre uniforme**: ningún issue se marca Done solo por completitud técnica — todos incluyen como criterio de aceptación la validación del **responsable del contrato** (rol del cliente, Mancomunidad, según LCSP art. 62), distinto de los roles técnicos internos de SpeedToCloud. Esta validación queda como paso explícito de interacción entre ambas partes tras la formalización.

## 5. Evidencia de aplicación práctica

Esta metodología no es una propuesta sin probar: es la que se ha seguido de facto desde el inicio del proyecto. Como muestra:

- 19 tasks documentadas en `panel/` con trazabilidad completa a su REQ/OPS de origen.
- Reconciliaciones reales del panel contra infraestructura/código (commits `panel de infra contra 02-26-infra-terraform...`, 2026-07-20; `multisitio por municipio implementado`, 2026-07-24; entre otros).
- Auditoría sistemática, issue a issue, de que los criterios de aceptación se cumplen contra el estado real (infraestructura, producción, Terraform) antes de considerarlos cerrados — proceso en curso sobre el conjunto de issues Done/In Progress del proyecto.

---

## 6. Pendiente

Esta metodología describe cómo SpeedToCloud planifica, organiza y documenta el trabajo. Lo que queda pendiente de fijar conjuntamente con la Mancomunidad es el **canal y cadencia formal de interacción** tras la formalización (reuniones de seguimiento, periodicidad, interlocutor designado por el cliente) — eso depende de una decisión organizativa de la Mancomunidad, no solo de la metodología interna de desarrollo.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
