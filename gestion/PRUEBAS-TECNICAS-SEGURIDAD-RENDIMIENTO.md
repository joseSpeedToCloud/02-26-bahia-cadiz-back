# Pruebas técnicas de rendimiento y seguridad

**Proyecto:** Plataforma Digital Bahía de Cádiz — Expediente 312/2026
**Requisito origen:** REQ-015 (apartado "pruebas técnicas de rendimiento y seguridad")
**Fecha:** 2026-07-30
**Estado:** Ejecutado contra el código y la infraestructura real. Hallazgos corregidos y verificados; ver apartado 4 para lo que queda fuera de alcance de este informe.

---

## 1. Rendimiento

Ya cubierto y verificado en REQ-145/TASK-004 con carga real contra producción (ver `panel/TASK-004-infra-autoescalado-api.md` y `02-26-infra-terraform/evidence/load-test-20260729-113011.json`): 6.776 peticiones OK / 0 fallos en el front, 7.941 peticiones OK / 0 fallos en el back, con escalado real de instancias confirmado en Cloud Monitoring. No se repite aquí para no duplicar evidencia ya registrada.

## 2. Seguridad — dependencias

### Front (`02-26-web-front`, `npm audit`)

44 vulnerabilidades reportadas (0 críticas, 32 altas, 7 moderadas, 5 bajas). **Las 32 "altas" están todas en `devDependencies`** (Angular CLI, ESLint, Karma, Lighthouse CI, chromedriver, Vite) — herramientas de build/test, no código que se sirve al usuario. El build de producción de Angular no incluye estas dependencias en el bundle final. Ninguna de las dependencias reales de producción (`@angular/*`, `@capacitor/*`, `@ionic/angular`, `leaflet`, `rxjs`, etc.) tiene vulnerabilidad reportada.

### Back (`02-26-web-back`, `composer.lock`)

`composer audit` propiamente dicho no se ha ejecutado (requiere red hacia la base de datos de advisories de Packagist/Drupal.org desde el contenedor), pero se verificó manualmente la versión de Drupal core contra los avisos de seguridad oficiales:

- **Versión desplegada (antes)**: `drupal/core 11.4.3`.
- **Hallazgo**: Drupal 11.4.4 (publicado 2026-07-15) corrige 3 avisos de seguridad de gravedad "moderadamente crítica": SA-CORE-2026-010 (divulgación de información), SA-CORE-2026-011 y SA-CORE-2026-012 (cross-site scripting).
- **Corregido en este trabajo**: `composer update drupal/core-recommended drupal/core-composer-scaffold --with-all-dependencies` (vía Docker, `composer:2`), `composer.lock` actualizado a `drupal/core 11.4.4`. Verificado localmente contra el stack real (`docker compose up -d --build`): `drush status` confirma `Drupal version: 11.4.4` y `Drupal bootstrap: Successful`; `drush updb` reporta "No pending updates" (sin migraciones de BD necesarias); JSON:API (`/jsonapi/node/evento`) sigue devolviendo contenido correctamente tras la actualización. Pendiente de build/deploy real a producción (Cloud Build tras el push).
- Módulos contrib (`admin_toolbar 3.6.3`, `ctools 4.1.0`, `pathauto 1.15.0`, `token 1.17.0`): sin avisos de seguridad conocidos en las versiones desplegadas a fecha de esta auditoría (verificación manual, no automatizada — recomendable repetir con `composer audit` real cuando haya un entorno con Composer disponible).

Fuentes: [Drupal 11.4.4 — release notes](https://www.drupal.org/project/drupal/releases/11.4.4), [Drupal 11.4.4, 11.3.14, and 10.6.13 Fix Three Security Issues](https://www.thedroptimes.com/71183/drupal-core-july-2026-security-fixes).

## 3. Seguridad — cabeceras HTTP y TLS (corregido en este trabajo)

Auditoría real contra producción (`curl -I`):

| Cabecera | Front (antes) | Back (antes) | Acción |
|---|---|---|---|
| `Strict-Transport-Security` | Ausente | Ausente | **Añadida** en ambos (`nginx.conf`, `Dockerfile` del back) |
| `X-Content-Type-Options` | Ausente | Presente (Drupal por defecto) | **Añadida** en el front |
| `X-Frame-Options` | Ausente | Presente (Drupal por defecto) | **Añadida** en el front |
| `Referrer-Policy` | Ausente | Ausente | **Añadida** en el front |
| `X-Powered-By: PHP/8.3.32` | N/A | Presente (revela versión de PHP) | **Ocultada** (`expose_php = Off`) |
| `Content-Security-Policy` | Ausente | Ausente | No añadida — ver apartado 4 |

TLS: gestionado por Cloud Run/Google Frontend (certificados y cifrado gestionados por la plataforma, no configurable por el proyecto) — fuera de alcance de una corrección propia, y ya usa TLS moderno por defecto de GCP.

**Cambios aplicados**: `02-26-web-front/nginx.conf`, `02-26-web-back/Dockerfile`. Pendientes de build/deploy real para verificarse en producción (no desplegados automáticamente por esta auditoría).

## 4. Pendiente — no resuelto por este informe

- **Content-Security-Policy** en front y back: mejora de seguridad real, pero requiere una fase de pruebas dedicada (mapear todos los orígenes de scripts/estilos/conexiones de Angular/Ionic/Leaflet y JSON:API) para no romper la aplicación al activarla — no se ha hecho aquí para no introducir una regresión sin validar.
- `composer audit` real contra la base de datos de advisories (no solo la verificación manual de versión hecha aquí) — pendiente de ejecutar en un entorno con acceso de red completo desde el contenedor.
- Pruebas de penetración (más allá de la comprobación de dependencias/cabeceras) — fuera de alcance de esta auditoría técnica básica.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
