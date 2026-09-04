# TASK-F03 — Usabilidad general del portal (aprendizaje, velocidad, incidencias, frustración, satisfacción, universalidad)

**Categoría:** Diseño/UX
**Requisito origen:** REQ-048 (SPEC-001, apartado 4.2 del pliego)
**Prioridad:** Media
**Story Points:** 3
**Labels:** frontend, ux, accesibilidad
**Estado:** Hecho (con matiz — ver limitación reconocida)

## Descripción técnica

Usabilidad centrada en facilidad de aprendizaje, velocidad, baja tasa de incidencias, bajos niveles de frustración, satisfacción subjetiva y universalidad, tal como exige el pliego. Es un requisito transversal, no una feature aislada: se audita el estado real del código (`02-26-web-front`) contra cada una de las seis dimensiones, se cierra el único hueco de código real encontrado, y se documentan honestamente las dos limitaciones que no se pueden cerrar solo con código.

## Entregable / Evidencia

**Facilidad de aprendizaje**: navegación consistente (`src/app/tabs/tabs.page.html`, 5 pestañas con icono+etiqueta en todas las pantallas), búsqueda e idioma accesibles desde la raíz (`src/app/app.component.html`), texto explicativo y labels descriptivos en login/registro.

**Velocidad**: lazy loading de todas las rutas + `PreloadAllModules` (`src/app/app-routing.module.ts`), Angular Service Worker con precarga de assets incluyendo WebP/AVIF (`ngsw-config.json`). Medido con Lighthouse real contra producción: **90/100 en rendimiento** (`evidence/INFORME-ACCESIBILIDAD.md`, sección 4).

**Baja tasa de incidencias**: confirmación explícita antes de acciones destructivas irreversibles (borrado de cuenta, `mi-cuenta.page.ts`), botones deshabilitados durante envío para evitar doble submit, mensajes de error legibles mapeados desde el backend. **Corregido en esta task**: `JsonApiService.fetchCollection()`/`fetchSingle()` (usadas por eventos, playas, rutas, aparcamientos, transporte) no reintentaban ante un corte de red breve — típico en móvil — y fallaban a la primera. Se añadió `retry({count: 2, delay: 500})` de RxJS.

**Bajos niveles de frustración**: degradación elegante si falla un servicio externo (Overpass API en rutas: `overpass.service.ts`, se sigue mostrando la ruta sin puntos de interés en vez de romper la pantalla), manejo de error consistente en login/registro/verificación de sesión, botón de retroceso con `defaultHref` explícito. Indicador de carga (`ion-spinner` con `aria-label`) presente en las 10 pantallas con estado de carga — no hay ninguna sin él.

**Universalidad**: selector de idioma ES/EN real (`LanguageService`, persistido en `localStorage`, con prefijo de idioma aplicado también a las peticiones JSON:API del contenido editorial, no solo a los textos de interfaz). Auditoría real de accesibilidad (Lighthouse + axe-core contra producción, `evidence/INFORME-ACCESIBILIDAD.md`, 30 jul 2026): **1.00/1.00 en Inicio/Eventos/Playas/Rutas, 0.96/1.00 en Mapa** (único hallazgo: tamaño de los marcadores del mapa, criterio WCAG AAA, no bloqueante en AA). Skip link al contenido principal, `aria-label`/`aria-hidden` correctos en controles icon-only, `alt` dinámico (`[alt]="p.title"`) en las 7 imágenes de contenido de la app, viewport sin bloquear pinch-to-zoom (decisión consciente documentada en el propio código, `src/index.html`).

**Satisfacción subjetiva**: sistema de encuestas (`encuestas.page.ts`, resultados agregados en porcentaje) y valoraciones 1-5 estrellas con comentario (`valoracion-usuario.component.ts`) sobre cualquier recurso, ambos enlazados desde navegación real (tarjeta en Inicio, resultado de búsqueda), no páginas huérfanas.

### Limitaciones reconocidas (no se dan por cerradas sin más)

- El propio informe de accesibilidad declara **pendiente** la prueba manual con lector de pantalla (NVDA/VoiceOver) y navegación 100% por teclado en flujos dinámicos (modales, cambios de contenido). Axe-core automático no cubre esto — queda fuera del alcance de esta task por no poder verificarse sin un dispositivo/lector real.
- No hay onboarding guiado ni tooltips explicativos más allá de navegación clara y textos de ayuda puntuales. Aceptable para un portal de consulta simple, pero si se espera evidencia explícita de "facilidad de aprendizaje" asistida, no existe.
