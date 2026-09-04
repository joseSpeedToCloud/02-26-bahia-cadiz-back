# TASK-F04 — Funcionalidades y contenidos diferenciados según tipo de usuario

**Categoría:** Gestión Usuarios
**Requisito origen:** REQ-049 (SPEC-001, apartado 4.2 del pliego)
**Prioridad:** Media
**Story Points:** 2
**Labels:** frontend, ux, cuentas
**Estado:** Hecho

## Descripción técnica

Funcionalidades y contenidos diferenciados según tipo de usuario (registrado o sin registro), tal como exige el pliego. Se audita el estado real de `02-26-web-front` y se corrige el único hueco de código encontrado en la diferenciación visual del header.

## Entregable / Evidencia

**Funcionalidades gated a usuario registrado** (renderizado condicional real vía `AuthService.cuentaActual()`, no solo cosmético — las peticiones al backend van sin cabecera `Authorization` si no hay sesión):
- Valoración de recursos (1-5 estrellas + comentario): sin cuenta, el widget interactivo ni se renderiza — solo un aviso con enlace a login. Presente en las 5 fichas de detalle (`evento-detalle`, `playa-detalle`, `ruta-detalle`, `transporte-detalle`, `aparcamiento-detalle`, vía `valoracion-usuario.component.ts`).
- Encuestas: mismo patrón, `encuestas.page.ts`/`.html`.
- Preferencias de cuenta (`mi-cuenta.page.html`): sección de "temas de interés" solo visible con sesión.

**Personalización real con impacto medible** (única preferencia persistida por cuenta, `temas_interes`, guardada en servidor vía `PATCH /api/portal/mi-cuenta`, `auth.service.ts:104-110`):
- Filtra los eventos recomendados en Inicio (`home.page.ts:66-77`).
- Reordena (destaca primero) los eventos afines en `/tabs/eventos` (`eventos.page.ts:36-39,76-77`).
- Preselecciona intereses en el asistente "Mi Plan de Viaje" (`mi-plan.page.ts:68-72`).

**Corregido en esta task**: el icono "Mi cuenta" de la portada (`home.page.html`) era idéntico siempre, sin distinguir sesión iniciada de anónima — único punto del header/menú global sin ninguna diferenciación visual. Ahora usa `person-circle` (relleno) con `aria-label` indicando el nombre del ciudadano si hay sesión, o `person-circle-outline` + "Iniciar sesión o crear cuenta" si no la hay. Reutiliza la suscripción a `cuentaActual()` que ya existía para la personalización, sin duplicar lógica.

### Alcance no cubierto (documentado, no oculto)

- **No hay guards de ruta** (`canActivate`): `/mi-cuenta`, `/encuestas` y `/mi-plan` son navegables libremente por URL sin sesión; el control es únicamente de contenido dentro de la página, no de acceso. No se ha tocado porque el requisito habla de "funcionalidades y contenidos diferenciados", no de control de acceso — pero si el pliego espera lo segundo, es un hueco real a revisar aparte.
- **Mi Plan de Viaje funciona igual sin cuenta** (guardado en `localStorage`, `plan-viaje.service.ts`), y la personalización de Inicio tiene una vía alternativa para anónimos basada en histórico local (`interacciones.service.ts`) — la frontera entre "registrado" y "anónimo" es más difusa de lo que un lector del requisito podría esperar, por diseño (no es un bug).
