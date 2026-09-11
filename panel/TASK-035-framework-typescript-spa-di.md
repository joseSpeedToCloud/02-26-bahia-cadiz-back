# TASK-035 — TypeScript, SPA con lazy loading, change detection y DI (REQ-065)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-065 (issue #86 / PPT ap. 4.2), muy relacionado con REQ-063 (TASK-033)
**Prioridad:** Media
**Story Points:** 1
**Labels:** frontend, angular, verificacion
**Estado:** En progreso — verificación técnica completa, pendiente de validación

## Descripción técnica

Framework con lenguaje fuertemente tipado (TypeScript), enrutamiento SPA nativo, lazy loading y detección de cambios automática, e inyección de dependencias incorporada.

## Verificación realizada (ya implementado, sin cambios necesarios)

- **TypeScript fuertemente tipado**: ver `TASK-033` (`tsconfig.json` con `strict: true` y variantes estrictas de plantillas/inyección).
- **Enrutamiento SPA nativo + lazy loading**: Angular Router con `loadChildren: () => import(...)`, confirmado real (no solo disponible): 13 apariciones en `src/app/app-routing.module.ts`, 5 en `src/app/tabs/tabs-routing.module.ts` — cada página carga su módulo bajo demanda, no todo en un único bundle.
- **Detección de cambios automática**: `zone.js` importado en `src/polyfills.ts:50` (mecanismo estándar de Angular para change detection automática sin código manual).
- **Inyección de dependencias incorporada**: `@Injectable({ providedIn: 'root' })` + inyección por constructor, patrón usado de forma consistente en todos los servicios (`LanguageService`, `AuthService`, `ContentService`, etc.) y componentes del proyecto.

## Entregable / Evidencia

Este documento, con los ficheros/líneas verificados arriba.

## Pendiente antes de marcar como Hecho

- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #86
