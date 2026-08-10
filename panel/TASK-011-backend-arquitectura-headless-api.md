# TASK-011 — Arquitectura headless: exposición de contenidos vía API estructurada y versionada

**Categoría:** CMS
**Requisito origen:** REQ-033 (SPEC-001)
**Prioridad:** Highest
**Story Points:** 8 *(estimado)*
**Labels:** backend, cms, drupal, api
**Estado:** Hecho

## Descripción técnica

Configurar Drupal en modalidad headless/decoupled, exponiendo todos los tipos de contenido a través de JSON:API (o GraphQL si se requiere en el portal), con versionado de la API para permitir evoluciones sin romper a los consumidores (portal web, app móvil).

## Entregable / Evidencia

Endpoints de API documentados (colección Postman/OpenAPI) + prueba de consumo desde un cliente externo.

**Avance:**

- Módulo core `jsonapi` habilitado (junto con `serialization`, `basic_auth` para autenticación de clientes externos vía HTTP Basic en dev).
- Probado con un cliente externo (`curl`): `GET /jsonapi` devuelve la colección raíz con todos los recursos disponibles (formato JSON:API 1.1); `GET /jsonapi/node/{bundle}` responde correctamente.
- **CORS habilitado** (`web/sites/default/services.yml`, `cors.config`): imprescindible para que un cliente real desde el navegador (la demo de app móvil en modo web, y el futuro Portal) pueda llamar al JSON:API — `curl` no lo detecta porque no aplica las restricciones CORS de un navegador, así que este hueco no salió hasta probar con un cliente real. **Importante:** `enabled: true` con `allowedOrigins: ['*']` es válido para desarrollo, pero antes de producción (TASK-008 ENS/RGPD) hay que restringirlo a los dominios reales del Portal/app.
- Detalle no obvio de Drupal: `sites/default/services.yml` **no se carga solo por existir** — hay que declararlo explícitamente en `settings.php` vía `$settings['container_yamls'][]`, si no Drupal usa silenciosamente los valores por defecto de `core.services.yml` sin avisar de ningún error.

## Cierre real (REQ-033, 2026-08-06)

Auditoría: lo pendiente que quedaba anotado ("Postman/OpenAPI, versionado de la API, decidir GraphQL") seguía sin hacer — se cierra ahora de verdad, no solo se documenta la intención.

**Documentación OpenAPI**: añadidos `drupal/openapi` (GPL-2.0-or-later) y `drupal/openapi_jsonapi` (GPL-2.0-or-later), que generan el spec (Swagger 2.0) directamente a partir de los recursos JSON:API reales expuestos — no es un documento estático que se desincroniza del código. Ruta real: `/openapi/jsonapi`.

**Bug real encontrado durante la verificación**: el permiso `access openapi api docs` no estaba concedido a ningún rol por defecto tras habilitar el módulo — el endpoint devolvía 403 incluso para un cliente anónimo, pese a que el propio JSON:API que documenta sí es público. Corregido: permiso concedido al rol `anonymous` (mismo criterio de acceso que el resto de la API de solo lectura), exportado a `config/sync/user.role.anonymous.yml`.

**Versionado de la API**: no existía ningún mecanismo. Creado módulo propio `web/modules/custom/bahia_cadiz_api/` (código nuestro, sin licencia de terceros) con un `EventSubscriber` que añade la cabecera `X-API-Version` a toda respuesta de `/jsonapi`, `/rest` y `/openapi`. Política de versionado documentada en el propio código (`ApiVersionSubscriber::API_VERSION`): un cambio incompatible en un campo/tipo de contenido ya consumido exige subir ese número y documentarlo en el CHANGELOG antes de desplegar; añadir campos/tipos nuevos no lo sube. Dentro de la rama mayor fijada en `composer.json` (`drupal/core-recommended: ^11`), Drupal core garantiza compatibilidad hacia atrás de JSON:API por su propia política; un cambio de formato base solo podría venir de un salto a Drupal 12, que sería una migración planificada, no silenciosa.

**GraphQL**: decisión tomada, no queda como pregunta abierta — no se expone. El pliego lo pide solo condicionalmente ("si se requiere en el portal"), y el portal Angular ya consume JSON:API directamente sin ninguna necesidad de consultas GraphQL. Añadirlo ahora sería una superficie extra sin consumidor real.

## Entregable / Evidencia (verificación real, no solo "debería funcionar")

- `curl -I http://localhost:8080/jsonapi/node/playa` → `X-API-Version: 1` presente en la respuesta real.
- `curl http://localhost:8080/openapi/jsonapi` (anónimo, sin login) → spec Swagger 2.0 real devuelto, con los tipos de contenido reales del proyecto como tags.
- Módulos `openapi`, `openapi_jsonapi`, `bahia_cadiz_api` confirmados `Enabled` tras `docker compose up -d --build` desde cero.
- Config exportada y copiada al repo (`config/sync/core.extension.yml`, `config/sync/user.role.anonymous.yml`).
- Entorno local levantado y desmontado (`docker compose up -d --build` / `docker compose down`) durante la verificación.

## Consultas semánticas por taxonomías UNE 178503 (etiqueta `une-178503` en REQ-033, 2026-08-07)

REQ-033 (issue #54) recibió después la etiqueta `une-178503`, que apunta a otra frase del mismo PPT ap. 4.1 no cubierta hasta ahora explícitamente: *"Las APIs deberán estar documentadas mediante OpenAPI y **permitir consultas semánticas basadas en las taxonomías UNE 178503**."* Se verificó de forma real, no se asumió que "ya debería funcionar" por tener JSON:API activo.

Resultado: **ya funciona de forma nativa**, sin necesidad de código nuevo — es una capacidad estándar de JSON:API sobre los campos de referencia a taxonomía que ya existían (taxonomías jerárquicas de REQ-029/TASK-018, etiquetas semánticas de REQ-030/TASK-019). Verificado real contra el backend local:

- Filtro exacto por término semántico: `GET /jsonapi/node/evento?filter[field_evento_etiquetas.name]=Gratuito` → devuelve el nodo real "Feria de Mayo de Cádiz".
- Mismo patrón sobre otro bundle: `GET /jsonapi/node/playa?filter[field_playa_etiquetas.name]=Familiar` → "Playa de la Victoria".
- Filtro por **taxonomía jerárquica** (`categoria_evento`, con términos padre/hijo): `GET /jsonapi/node/evento?filter[field_evento_categoria.name]=Musica` (término hijo de "Cultura y ocio") → 2 eventos reales.
- Combinación con `include` para traer los términos de la taxonomía en la misma respuesta: `?filter[field_playa_etiquetas.name]=Accesible&include=field_playa_etiquetas`.
- Búsqueda semántica **parcial**, no solo exacta, con el operador `CONTAINS` de JSON:API (`filter[etiqueta-cnt][path]=field_evento_etiquetas.name&filter[etiqueta-cnt][value]=rat&filter[etiqueta-cnt][operator]=CONTAINS`) → coincide "Gratuito" con "rat".
- El vocabulario en sí es descubrible por un cliente externo sin credenciales: `GET /jsonapi/taxonomy_term/etiquetas_semanticas` y `GET /jsonapi/taxonomy_term/categoria_evento`.

No hay ninguna pieza de código pendiente para esto: la combinación de (a) los campos de referencia a taxonomía ya modelados conforme a UNE 178503, (b) JSON:API core habilitado, y (c) los vocabularios expuestos como recursos propios, ya satisface el requisito literal. Entorno local levantado y desmontado durante esta verificación.

Pendiente exclusivamente de validación por el responsable del contrato.
