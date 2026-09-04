# Panel de Tasks — Frontend (Plataforma Digital Bahía de Cádiz)

Deriva de [`especificaciones/SPEC-001-plataforma-bahia-cadiz.md`](../especificaciones/SPEC-001-plataforma-bahia-cadiz.md). Cubre la parte de **frontend**: portal web (`02-26-web-front`, Angular/Ionic) — agenda de eventos, playas, movilidad sostenible (mapa, rutas, transporte, aparcamiento) y app móvil.

Hermano de [`panel/README.md`](README.md) (backend) — mismo SPEC, misma Mancomunidad, repos de código distintos (`02-26-web-front` vs `02-26-web-back`). Numeración de tasks con prefijo `F` para no colisionar con las del backend (`TASK-001`…`TASK-024`).

> Este panel se crea el 2026-08-07, más tarde que el de backend — buena parte del trabajo de frontend ya está hecha (ver los `TASK-0XX` del backend que mencionan "frontend" en sus labels, p. ej. TASK-019/020/021, y el trabajo de esta sesión sobre mapa/fichas de detalle) pero no estaba trackeada aquí. Las tasks de este panel se irán completando task por task a medida que aparezcan REQ-XXX de frontend en el checklist, no como un backfill retroactivo completo de golpe.

## Portal web — Agenda, Playas y Movilidad

| ID | Título | Prioridad | SP | Estado |
|----|--------|-----------|----|--------|
| [TASK-F01](TASK-F01-portal-web-agenda-playas-movilidad.md) | Desarrollo y puesta en producción del portal (agenda cultural, playas, movilidad sostenible) | Alta | 8 *(estimado)* | **Hecho** |
| [TASK-F02](TASK-F02-visualizacion-modulos-contenido.md) | Visualización real de los 4 módulos (gestor de contenidos, agenda, playas, movilidad), verificado en producción | Media | 2 *(estimado)* | **Hecho** |
| [TASK-F03](TASK-F03-usabilidad-general-portal.md) | Usabilidad general (aprendizaje, velocidad, incidencias, frustración, satisfacción, universalidad) | Media | 3 | **Hecho (con matiz)** |

## Fuera de alcance de este panel (por ahora)

App móvil nativa empaquetada (PWA→Android/iOS vía Capacitor u equivalente) más allá de lo que Ionic ya provee de base — no auditado todavía contra el pliego (ap. 4.2: "se podrá empaquetar y ejecutar como aplicación híbrida... sin necesidad de reescritura").
