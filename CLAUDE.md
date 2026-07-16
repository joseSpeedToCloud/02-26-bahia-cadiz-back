# Plataforma Digital Bahía de Cádiz — backend

Proyecto Drupal headless + infraestructura GCP/Terraform para la licitación de la Mancomunidad de Municipios de la Bahía de Cádiz (Expediente 312/2026).

## Convención de trabajo: specs y tasks

- `especificaciones/SPEC-XXX-*.md` — especificaciones de alto nivel (qué se construye y por qué). Cada SPEC agrupa un conjunto de tasks.
- `panel/TASK-XXX-*.md` — una task por entregable concreto, con: Categoría, Requisito origen (REQ del pliego), Prioridad, Story Points, Labels, Estado, Descripción técnica y Entregable/Evidencia.
- `panel/README.md` — índice de todas las tasks con su estado y el orden recomendado de ejecución.

Antes de empezar a trabajar en una task:
1. Leer la SPEC de la que depende para tener el contexto completo.
2. Comprobar el "Requisito origen" — si hay dudas sobre el alcance exacto, consultar el pliego (PPT/PCAP) o el checklist original antes de asumir nada.

Al completar una task, actualizar su "Estado" en el propio archivo `TASK-XXX-*.md` y en la tabla de `panel/README.md` (Pendiente → En progreso → Hecho).

## Restricciones del contrato a tener siempre presentes

- No subcontratar el desarrollo principal.
- Todo el código y documentación son propiedad de la Mancomunidad; entrega en Git obligatoria.
- Librerías de terceros: solo GPL/MIT/Apache o licencias comerciales perpetuas transferibles.
- Documentación en formato digital (condición especial A) y datacenter con certificación energética (condición especial B) — ambas penalizan un 3% del contrato si se incumplen.
- SLA y tiempos de intervención comprometidos tienen carácter contractual, no solo de propuesta.

## Entorno local

`docker-compose.yml` en la raíz levanta Drupal + MariaDB para desarrollo (`docker compose up -d`, acceso en `http://localhost:8080`).
