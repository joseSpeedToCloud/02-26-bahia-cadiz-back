# TASK-011 — Arquitectura headless: exposición de contenidos vía API estructurada y versionada

**Categoría:** CMS
**Requisito origen:** REQ-033 (SPEC-001)
**Prioridad:** Highest
**Story Points:** 8 *(estimado)*
**Labels:** backend, cms, drupal, api
**Estado:** En progreso

## Descripción técnica

Configurar Drupal en modalidad headless/decoupled, exponiendo todos los tipos de contenido a través de JSON:API (o GraphQL si se requiere en el portal), con versionado de la API para permitir evoluciones sin romper a los consumidores (portal web, app móvil).

## Entregable / Evidencia

Endpoints de API documentados (colección Postman/OpenAPI) + prueba de consumo desde un cliente externo.

**Avance:**

- Módulo core `jsonapi` habilitado (junto con `serialization`, `basic_auth` para autenticación de clientes externos vía HTTP Basic en dev).
- Probado con un cliente externo (`curl`): `GET /jsonapi` devuelve la colección raíz con todos los recursos disponibles (formato JSON:API 1.1); `GET /jsonapi/node/{bundle}` responde correctamente.
- Pendiente para cerrar la task: generar la colección Postman/documento OpenAPI, definir la estrategia de versionado de la API (todavía no hay tipos de contenido custom — eso llega con TASK-015), y decidir si además se expone GraphQL.
