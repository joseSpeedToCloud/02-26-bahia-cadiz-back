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

Instancia Drupal desplegada (entorno dev) con perfil de instalación y estructura de repo documentados.

**Estructura del repo (implementada):**

- Proyecto gestionado por Composer (`composer.json`/`composer.lock`, Drupal 11 core-recommended), no por volúmenes anónimos de Docker — requisito para poder entregar el código en Git (cláusula de propiedad intelectual del pliego).
- `docker/drupal/Dockerfile`: construye la imagen a partir de `drupal:11-apache`, instala dependencias desde el `composer.lock` versionado, y copia el código propio (`web/sites/default/settings.php`, `web/modules/custom/`, `web/themes/custom/`, `config/sync/`).
- `docker-compose.yml`: construye la imagen local; persiste datos en volúmenes (`db-data` para MariaDB, `drupal-files` para ficheros públicos) — el código vive en la imagen/repo, no en volúmenes.
- Perfil de instalación: `standard`, instalado vía `drush site:install` (ver comando en el historial de la sesión de setup).
- Configuración exportada a `config/sync/` (150 ficheros) — configuration-as-code versionada en Git, incluye `admin_toolbar` y `pathauto` habilitados.
- Persistencia verificada: `docker compose down` (elimina contenedores) + `docker compose up` recupera el sitio y el contenido sin pérdida de datos (probado con un nodo de prueba).
- Pendiente de repo: **commit inicial** (Git ya inicializado localmente; el usuario ha preferido revisar antes de comitear).
