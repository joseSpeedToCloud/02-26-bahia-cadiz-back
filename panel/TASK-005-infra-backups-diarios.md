# TASK-005 — Backups diarios con retención de 30 días

**Categoría:** Soporte/Garantía
**Requisito origen:** REQ-146 (SPEC-001), relacionado con REQ-086
**Prioridad:** High
**Story Points:** 3
**Labels:** infra, terraform, backup
**Estado:** Hecho

## Descripción técnica

Configurar Cloud SQL/Storage con copias de seguridad automáticas diarias y política de retención de 30 días, conforme al ratio comprometido en la oferta.

## Entregable / Evidencia

**Implementado en `02-26-infra-terraform/Fase2/3.CloudSQL`**: backups diarios de la instancia Cloud SQL con 30 días de retención, confirmado explícitamente en el README de ese repo ("Backups diarios de Cloud SQL con 30 días de retención (obligatorio, PPT cláusula 4.7)").

**Prueba de restauración completa (2026-08-03)**: clonado real desde el backup más reciente (`gcloud sql instances clone`), conectado con el usuario real de producción (`bahiacadiz_admin` — no `drupal`, corrigiendo la confusión de la primera prueba) y verificado el contenido real de las tablas: conteos por tipo de contenido (`aparcamiento`: 3, `aviso_trafico`: 49, `evento`: 2, `parte_meteorologico`: 2, `playa`: 2, `ruta`: 3, `transporte`: 5) coinciden con lo esperado, y títulos reales (Feria de Mayo de Cádiz, Playa de la Victoria, etc.) confirmados nodo a nodo. Instancia de prueba borrada tras la verificación. Sin huecos.
