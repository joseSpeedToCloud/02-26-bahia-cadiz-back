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
- **CORS habilitado** (`web/sites/default/services.yml`, `cors.config`): imprescindible para que un cliente real desde el navegador (la demo de app móvil en modo web, y el futuro Portal) pueda llamar al JSON:API — `curl` no lo detecta porque no aplica las restricciones CORS de un navegador, así que este hueco no salió hasta probar con un cliente real. **Importante:** `enabled: true` con `allowedOrigins: ['*']` es válido para desarrollo, pero antes de producción (TASK-008 ENS/RGPD) hay que restringirlo a los dominios reales del Portal/app.
- Detalle no obvio de Drupal: `sites/default/services.yml` **no se carga solo por existir** — hay que declararlo explícitamente en `settings.php` vía `$settings['container_yamls'][]`, si no Drupal usa silenciosamente los valores por defecto de `core.services.yml` sin avisar de ningún error.
- Pendiente para cerrar la task: generar la colección Postman/documento OpenAPI, definir la estrategia de versionado de la API, y decidir si además se expone GraphQL.
