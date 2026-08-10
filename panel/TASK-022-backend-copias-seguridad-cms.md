# TASK-022 — Copias de seguridad sencillas desde el CMS (contenido, ficheros y base de datos)

**Categoría:** CMS
**Requisito origen:** REQ-031 (Checklist_PlataformaDigitalBahiaCadiz.xlsx, hoja "Checklist Completo" — PPT ap. 4.1)
**Prioridad:** Medium
**Story Points:** 3 *(estimado)*
**Labels:** backend, cms, drupal, backup
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.1 (CMS), cita literal: *"Permitirá realizar de forma sencilla e intuitiva copias de seguridad a los niveles de contenido, estructura de carpetas y base de datos."*

Este requisito es **distinto** de TASK-005 (backups diarios, retención 30 días): TASK-005 cubre los backups automáticos de infraestructura (Cloud SQL, vía Terraform, PPT ap. 4.7 — la garantía de continuidad operativa). REQ-031 pide que el propio **CMS** ofrezca una copia de seguridad sencilla e intuitiva, accionable por un perfil autorizado desde dentro del gestor de contenidos — no solo un snapshot automático a nivel de infraestructura que el equipo técnico gestiona por su cuenta.

Auditoría real (2026-08-06): no existía ningún módulo ni mecanismo de backup a nivel de CMS — `composer.json` no incluía ningún paquete de backup, y no había ninguna task del panel que cubriera este punto (README solo tenía TASK-005, de infraestructura).

## Trabajo realizado

- Añadido `drupal/backup_migrate` (GPL-2.0-or-later, dentro de la lista de licencias permitidas por el contrato, PPT cláusula 14) a `composer.json`/`composer.lock`.
- Módulo habilitado de forma idempotente en `entrypoint.sh` (`drush pm:enable backup_migrate --yes`), tanto en la instalación fresca como en las actualizaciones.
- Configurado `$settings['file_private_path']` (nuevo, faltaba por completo) en `settings.php` — sin esto, ningún backup puede generarse: el stream wrapper `private://` que usa el módulo no tiene dónde escribir. Directorio creado/permisos ajustados en `entrypoint.sh` (disco efímero del contenedor, pensado para descargar el backup al momento, no para persistir entre despliegues — eso ya lo cubre TASK-005).
- **Bug real encontrado durante la verificación**: `drupal/backup_migrate` **no soporta PostgreSQL** — su único origen de "base de datos por defecto" (`DefaultDBSourcePlugin`) comprueba explícitamente `driver == 'mysql'` y se autoexcluye en cualquier otro motor; no hay ni una referencia a `pgsql`/`postgres` en todo el módulo. Este proyecto usa Cloud SQL para PostgreSQL, así que sin más trabajo el módulo solo cubriría ficheros, no base de datos.
- **Corregido con un módulo propio** (`web/modules/custom/backup_migrate_pgsql/`, código nuestro, sin dependencia ni problema de licencia): un origen de backup adicional (`PgsqlDBSourcePlugin` + `PgsqlSource`) que replica el patrón del `DefaultDBSourcePlugin` de MySQL pero usando `pg_dump`/`psql` (binarios ya presentes en la imagen vía `postgresql-client`, instalados para el chequeo de arranque). Detalles de seguridad: los argumentos se pasan a `Symfony\Process` como array (nunca se interpola una cadena de shell) y la contraseña viaja por la variable de entorno `PGPASSWORD`, nunca en `argv` (visible vía `ps aux`) ni en config versionada — las credenciales se leen en caliente de la conexión real de Drupal en cada backup, igual que hace el plugin de MySQL original.
- Config exportada a `config/sync/` (entidades `backup_migrate.*`, incluida la nueva `default_pgsql_db`) para que quede versionada y no se reinstale desde cero en cada despliegue.

## Entregable / Evidencia

Verificado en local (`docker compose up -d --build`) con Playwright contra el admin real, no solo "debería funcionar":

- `/admin/config/development/backup_migrate` (ruta real del módulo — no `/admin/config/system/...` como cabría esperar) carga correctamente tras login.
- El desplegable "Backup Source" ofrece `Default Drupal Database (PostgreSQL)`, `Private Files Directory` y `Public Files Directory` — la fuente PostgreSQL aparece junto a las de ficheros, igual que aparecería `Default Database` en un sitio MySQL.
- Backup real ejecutado con un clic ("Backup now") con origen "PostgreSQL" y destino "Download": se descargó `backup-2026-08-06T12-33-31.pgsql.gz` (991.664 bytes). Descomprimido y verificado: **37.434 líneas, 281 `CREATE TABLE`**, y contenido real confirmado (`grep` localizó la fila del nodo real "Feria de Mayo de Cádiz" en el dump).
- Módulos `backup_migrate` y `backup_migrate_pgsql` confirmados `Enabled` vía `drush pm:list`; entidad de origen `default_pgsql_db` confirmada vía `drush backup_migrate:sources`.
- Entorno local desmontado (`docker compose down`) tras la verificación.
