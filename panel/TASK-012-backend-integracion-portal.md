# TASK-012 — CMS como backend unificado y repositorio principal de contenidos

**Categoría:** CMS
**Requisito origen:** REQ-018, REQ-019 (SPEC-001)
**Prioridad:** High
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, integracion
**Estado:** Hecho

## Descripción técnica

Definir el CMS como repositorio principal de contenidos de toda la plataforma, garantizando que sea integrable con el resto de apps/repositorios del proyecto (portal, app móvil, módulos de Agenda/Playas/Movilidad). Establecer los contratos de datos y convenciones de API que consumirán los demás módulos.

## Entregable / Evidencia

Documento de arquitectura de integración + esquema de datos compartido entre CMS y consumidores: [`gestion/ARQUITECTURA-INTEGRACION.md`](../gestion/ARQUITECTURA-INTEGRACION.md) (redactado 2026-07-30, verificado contra el código real de ambos repos: JSON:API solo lectura, CORS, inyección de URL en runtime, y el hallazgo de que `aviso_trafico`/`parte_meteorologico` aún no tienen consumidor en el front).

Pendiente: validación del responsable del contrato (Mancomunidad).
