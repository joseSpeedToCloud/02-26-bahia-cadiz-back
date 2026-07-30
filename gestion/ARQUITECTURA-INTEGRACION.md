# Arquitectura de integración CMS ↔ portal y esquema de datos compartido

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-013 (apartado "diseño de arquitectura técnica y base de datos"), TASK-012
**Fecha:** 2026-07-30
**Estado:** Documenta la arquitectura real ya implementada y desplegada (no un diseño a futuro).

---

## 1. Visión general

```
Angular/Ionic (Cloud Run, público)  ──HTTPS/JSON:API (solo lectura)──▶  Drupal 11 (Cloud Run, público)
                                                                              │
                                                                              ▼
                                                              Cloud SQL PostgreSQL 16
                                                              (IP pública + SSL obligatorio)
```

Drupal es el **repositorio único de contenidos**; el portal no persiste datos propios, consume la API del CMS. No hay más consumidores hoy (la app móvil es el mismo build Angular/Ionic empaquetado con Capacitor, no un cliente API distinto).

## 2. Contrato de API

- **Protocolo**: JSON:API (módulo core `jsonapi` de Drupal, habilitado — `config/sync/core.extension.yml`).
- **Modo**: solo lectura — `config/sync/jsonapi.settings.yml` fija `read_only: true`. No hay escritura desde el front vía API.
- **Rutas**: convención por defecto de Drupal, sin overrides (`jsonapi_extras` no está instalado) — `/jsonapi/node/<bundle>` para cada content type: `evento`, `playa`, `ruta`, `transporte`, `aparcamiento`, `aviso_trafico`, `parte_meteorologico`.
- **Autenticación**: ninguna. Es una API pública de solo lectura; el único control de acceso es el whitelisting de CORS (`web/sites/default/services.yml`), que restringe el origen permitido al dominio real del portal en Cloud Run (`allowedOrigins`, un único valor, sin wildcard — ver TASK-008/OPS-008).
- **Descubribilidad del endpoint**: la URL base del backend no está hardcodeada en el build del front — se inyecta en tiempo de ejecución vía la variable de entorno `DRUPAL_API_URL` (`docker-entrypoint.sh`), leída en el cliente como `window.__env.apiUrl` (`src/app/services/jsonapi.service.ts`). Esto permite desplegar el mismo artefacto de front contra distintos backends (dev/preprod/prod) sin rebuild.

## 3. Consumo desde el portal

- `src/app/services/jsonapi.service.ts`: cliente JSON:API genérico — parsea el bloque `data`/`included` y resuelve `relationships` manualmente (Drupal no devuelve los datos desnormalizados).
- `src/app/services/content.service.ts` y `movilidad.service.ts`: consumen los endpoints reales (`/jsonapi/node/evento`, `/playa`, `/ruta`, `/transporte`, `/aparcamiento`) con `include=` para relaciones, y mapean la respuesta a interfaces TypeScript propias (`src/app/models/content.ts`, `movilidad.ts`).
- **Hallazgo de esta auditoría**: `aviso_trafico` y `parte_meteorologico` existen como content types en Drupal (con su API expuesta) pero **no tienen consumidor todavía en el código Angular** — no hay ningún servicio que llame a esos dos endpoints. No es un fallo de arquitectura (la API ya los soporta sin cambios), pero queda como trabajo pendiente de integración en el front, fuera del alcance de este documento.

## 4. Esquema de datos

No existe (ni se ha diseñado) un esquema SQL a mano: Drupal gestiona el esquema físico de PostgreSQL automáticamente a partir de la configuración de entidades/campos (`config/sync/node.type.*.yml`, `field.storage.node.*.yml`, `field.field.node.*.yml`), aplicada vía `drush config:import`. La fuente de verdad del esquema es esa configuración versionada en Git, no las tablas en sí.

- **Motor**: Cloud SQL `POSTGRES_16` (`02-26-infra-terraform/Fase2/3.CloudSQL/main.tf`), `ssl_mode = "ENCRYPTED_ONLY"`.
- **Contrato compartido con el front**: no existe un contrato formal (OpenAPI/JSON Schema) entre back y front. Lo más cercano son las interfaces TypeScript escritas a mano en `src/app/models/*.ts`, que reflejan los campos reales de Drupal pero sin generación automática ni validación de que ambos lados no diverjan — es un contrato implícito, mantenido manualmente.

## 5. Pendiente

- **Contrato formal de API**: no hay OpenAPI/Swagger generado a partir de los `resource types` de JSON:API. Sería una mejora recomendable (evitaría que el front y el back diverjan silenciosamente al añadir/renombrar campos), pero no es una carencia bloqueante hoy — el proceso de trabajo actual (cambio en Drupal → actualización manual de la interfaz TypeScript correspondiente) ha funcionado sin incidencias hasta la fecha.
- **Integración de `aviso_trafico`/`parte_meteorologico` en el front**: ver hallazgo del apartado 3.
- **Exposición JSON-LD/schema.org**: señalada en el propio código (`field_playa_latitud`, comentario "exposicion JSON-LD/schema.org (TASK-019)") y en `gestion/MAPEO-UNE-178503.md` — corresponde a TASK-019, no a este documento.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
