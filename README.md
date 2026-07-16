# Backend Drupal — Plataforma Digital Bahía de Cádiz (Expediente 312/2026)

CMS headless (Drupal 11) que expone JSON:API para el Portal (`../02-26-bahia-cadiz-front`). Ver `especificaciones/SPEC-001-plataforma-bahia-cadiz.md` y `panel/README.md` para el detalle de tasks.

## Arrancar en local

```bash
cp .env.example .env        # y genera un DRUPAL_HASH_SALT propio (ver comentario en el fichero)
docker compose build
docker compose up -d
```

Con esto se levanta Drupal (`http://localhost:8080`) + MariaDB. El código (composer.json/lock, `config/sync`, `web/sites/default/settings.php` y `services.yml`) está versionado en Git y se hornea en la imagen; los datos (base de datos y ficheros públicos) viven en volúmenes Docker, no en el repo.

## Bootstrap de una base de datos vacía (primera vez / entorno nuevo, p. ej. GCP)

El paso `docker compose up` **no instala Drupal por sí solo** — solo levanta el contenedor con el código. Si la base de datos está vacía (recién creada, p. ej. una instancia nueva de Cloud SQL), hay que ejecutar esto una vez:

```bash
# 1. Instalar Drupal (perfil standard) contra la BD configurada en las variables de entorno
docker compose exec drupal vendor/bin/drush site:install standard \
  --db-url=mysql://drupal:drupal@db:3306/drupal \
  --site-name="Plataforma Digital Bahia de Cadiz" \
  --account-name=admin --account-pass=admin \
  --no-interaction -y

# 2. Habilitar los módulos que necesita la API headless y la administración
docker compose exec drupal vendor/bin/drush pm:enable jsonapi basic_auth admin_toolbar pathauto --yes

# 3. Crear los tipos de contenido (Evento, Playa, Ruta), taxonomías y patrones de URL
#    (script idempotente, se puede volver a ejecutar sin duplicar nada)
docker compose cp scripts/provision-content-types.php drupal:/opt/drupal/provision-content-types.php
docker compose exec drupal vendor/bin/drush php:script /opt/drupal/provision-content-types.php

# 4. Exportar la configuración resultante para que quede versionada
docker compose exec drupal vendor/bin/drush config:export --yes
docker compose cp drupal:/opt/drupal/config/sync/. ./config/sync/
```

En un entorno ya provisionado (GCP con Cloud SQL con datos), **no hace falta repetir esto** — basta con desplegar la imagen; `config/sync` ya contiene la configuración base (tipos de contenido, roles, taxonomías) para importarla con `drush config:import` si se parte de una BD limpia con esa config ya sincronizada.

## ⚠️ Antes de desplegar a un entorno público (GCP)

- **CORS está abierto a `*`** (`web/sites/default/services.yml`) — necesario para probar el Portal en local. Antes de producción hay que restringir `allowedOrigins` al dominio real del Portal desplegado.
- **`DRUPAL_HASH_SALT`** en `.env` es un valor de desarrollo inseguro — generar uno nuevo por entorno (`openssl rand -hex 32`) e inyectarlo vía Secret Manager, nunca committearlo.
- Los ficheros públicos (imágenes subidas) usan un volumen Docker local — en Cloud Run (sin disco persistente entre instancias) hay que moverlos a Cloud Storage antes de producción (pendiente, ver `panel/TASK-004-infra-autoescalado-api.md`).
- Los datos de ejemplo (3 eventos, 3 playas, 3 rutas) son contenido de demostración — sustituir por contenido real antes de producción.

## Estructura del repo

- `docker/drupal/Dockerfile` — imagen del backend (composer + código propio).
- `docker-compose.yml` — orquestación local (Drupal + MariaDB).
- `web/sites/default/settings.php` / `services.yml` — configuración de entorno y CORS.
- `config/sync/` — configuración de Drupal exportada (tipos de contenido, roles, taxonomías, vistas...).
- `scripts/provision-content-types.php` — aprovisionamiento idempotente de Evento/Playa/Ruta.
- `especificaciones/`, `panel/` — specs y tasks (convención documentada en `CLAUDE.md`).
