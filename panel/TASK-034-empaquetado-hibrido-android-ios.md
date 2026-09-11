# TASK-034 — Empaquetado híbrido Android/iOS sin reescritura (REQ-064)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-064 (issue #85 / PPT ap. 4.2), muy relacionado con REQ-063 (TASK-033)
**Prioridad:** Media
**Story Points:** 1
**Labels:** frontend, capacitor, mobile, verificacion
**Estado:** En progreso — verificación técnica completa, pendiente de validación

## Descripción técnica

Empaquetado y ejecución como aplicación híbrida en Android e iOS sin reescritura del código fuente principal.

## Verificación realizada (ya implementado, sin cambios necesarios)

- `02-26-web-front/capacitor.config.ts:6`: `webDir: 'www'` — el mismo directorio de salida que genera `ng build` (`angular.json` → `outputPath: "www"`). Es decir, Android e iOS empaquetan el **mismo bundle web**, no una reescritura ni un código fuente distinto por plataforma.
- `android/`: proyecto Gradle real y completo (`build.gradle`, `gradlew`, `settings.gradle`, `capacitor-cordova-android-plugins`), no una carpeta vacía o de plantilla.
- `ios/`: proyecto Xcode real (`App/`, `capacitor-cordova-ios-plugins`, `debug.xcconfig`).
- Complementa la verificación ya hecha en `TASK-033` (REQ-063) sobre el mismo mecanismo (Capacitor).

## Entregable / Evidencia

Este documento + `capacitor.config.ts` y las carpetas `android/`/`ios/` reales del repo.

## Pendiente antes de marcar como Hecho

- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #85
