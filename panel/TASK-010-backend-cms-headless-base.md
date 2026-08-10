# TASK-010 — Selección y puesta a punto base del CMS headless (Drupal)

**Categoría:** CMS
**Requisito origen:** REQ-023, REQ-037 (SPEC-001)
**Prioridad:** Highest
**Story Points:** 5 *(estimado — el checklist original no detalla SP para esta categoría en esta sesión)*
**Labels:** backend, cms, drupal
**Estado:** Hecho

## Descripción técnica

Instalar y configurar la base de Drupal como CMS de código abierto, modular y escalable (con comunidad activa y soporte a largo plazo), que sirva de fundamento al resto de tasks del módulo CMS. Definir perfil de instalación, módulos core habilitados y estructura inicial del repositorio de código.

## Entregable / Evidencia

Instancia Drupal desplegada (entorno dev y producción real en Cloud Run) con perfil de instalación y estructura de repo documentados.

**Nota de corrección (2026-08-07)**: la evidencia original de esta task describía un `docker/drupal/Dockerfile` basado en `drupal:11-apache` con MariaDB, que ya no corresponde al estado real del proyecto — corregido aquí para no dejar documentación desactualizada sirviendo de evidencia de cumplimiento.

**Estructura del repo (real, verificada 2026-08-07):**

- Proyecto gestionado por Composer (`composer.json`/`composer.lock`, `drupal/core-recommended: ^11`), no por volúmenes anónimos de Docker — requisito para poder entregar el código en Git (cláusula de propiedad intelectual del pliego, PPT ap. 14).
- `Dockerfile` (raíz del repo): imagen base `php:8.3-apache` (no la imagen oficial `drupal:*-apache`, para controlar exactamente las extensiones PHP/paquetes del sistema — `pdo_pgsql`, `gd`, `gcsfuse`, `postgresql-client`), instala dependencias vía `composer install --no-dev` desde el `composer.lock` versionado, y copia el código propio (`config/`, `scripts/`, `web/modules/custom/`, `web/sites/default/settings.php`/`services.yml`).
- `docker-compose.yml`: construye la imagen local con Postgres 16 (no MariaDB — la base de datos real, tanto local como en producción vía Cloud SQL, es PostgreSQL); persiste datos en volúmenes nombrados (`db-data`, `drupal-files`) — el código vive en la imagen/repo, no en volúmenes.
- Perfil de instalación: `drush site:install --existing-config` (instala directamente desde la configuración versionada en `config/sync/`, no el perfil `standard` por defecto sin configurar).
- Configuración exportada a `config/sync/` (bajo control de versiones) — configuration-as-code, incluye `admin_toolbar`, `pathauto`, `backup_migrate`, `openapi_jsonapi`, `rdf`, entre otros módulos habilitados a lo largo de esta sesión.
- Persistencia verificada repetidamente a lo largo de la sesión: `docker compose down`/`up --build` recupera el sitio y el contenido sin pérdida de datos entre reconstrucciones de imagen.
- Repositorio con historial real de commits (ya no está pendiente el commit inicial, como decía la evidencia original).
- El propio volumen de trabajo de esta sesión (JSON:API, taxonomías UNE 178503, multimedia, multiidioma, RDF/schema.org, OpenAPI, backups) es en sí mismo la prueba de que Drupal "garantiza el cumplimiento de todos los requisitos funcionales y técnicos definidos" citado literalmente por REQ-037 — no ha aparecido ningún requisito del pliego que Drupal no pudiera satisfacer (con o sin módulo contrib adicional, siempre dentro de las licencias permitidas por el contrato).
