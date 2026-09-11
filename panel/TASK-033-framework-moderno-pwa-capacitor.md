# TASK-033 — Framework moderno, PWA offline y contenedores nativos (REQ-063)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-063 (issue #84 / PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 1
**Labels:** frontend, pwa, capacitor, verificacion
**Estado:** En progreso — verificación técnica completa, pendiente de validación

## Descripción técnica

Framework moderno basado en componentes reutilizables, tipado estático, binding bidireccional y separación entre lógica y vista; despliegue como PWA con soporte offline y ejecución móvil mediante contenedores nativos.

## Verificación realizada (ya implementado, sin cambios necesarios)

- **Framework + tipado estático**: Angular 20 (`@angular/core: 20.3.25`) + TypeScript con `strict: true`, `strictTemplates`, `strictInjectionParameters`, `strictInputAccessModifiers` (`tsconfig.json:8,30-32`).
- **Componentes reutilizables y separación lógica/vista**: patrón `.ts`/`.html`/`.scss` en cada componente y página, consistente en todo `02-26-web-front/src/app`.
- **Binding bidireccional**: `[(ngModel)]` en uso real (no solo disponible) en `login.page.html:21,24`, `registro.page.html:21,24`, `valoracion-usuario.component.ts:35`, entre otros.
- **PWA con soporte offline**: `serviceWorker: true` + `ngswConfigPath: "ngsw-config.json"` en `angular.json`; `ngsw-config.json` y `public/manifest.webmanifest` reales en el repo.
- **Ejecución móvil mediante contenedores nativos**: Capacitor — `capacitor.config.ts`, carpetas `android/` e `ios/` reales (mismo código empaquetado, sin app nativa separada que pueda divergir).

## Entregable / Evidencia

Este documento, con los ficheros/líneas verificados arriba.

## Pendiente antes de marcar como Hecho

- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #84
