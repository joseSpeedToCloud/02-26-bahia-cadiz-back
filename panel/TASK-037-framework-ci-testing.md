# TASK-037 — Framework recomendado, CI y pruebas automatizadas (REQ-067)

**Categoría:** Infraestructura/Cloud
**Requisito origen:** REQ-067 (issue #88 / PPT ap. 4.2) — "se recomienda" (no imperativo en el detalle, aunque el issue lo categoriza como imperativo)
**Prioridad:** Media
**Story Points:** 3
**Labels:** ci, testing, angular, verificacion
**Estado:** En progreso — en verificación local

## Descripción técnica

Recomendación no excluyente de frameworks con soporte oficial de Google, herramientas de CI y pruebas automatizadas; uso recomendado de Angular junto con Ionic o equivalentes funcionales.

## Verificación realizada

**Cumple:**
- Framework con soporte oficial de Google: **Angular** (+ Ionic), ya elegido y en uso en todo el proyecto.
- Herramientas de CI: **Cloud Build** real, ya verificado en sesiones anteriores (triggers `gcp-cbt-front-02-26-bh`/`gcp-cbt-back-02-26-bh`).

**Hueco encontrado (antes de esta task):**
- Existían scripts de test (`ng test`, `lhci autorun` para Lighthouse CI, `axe` para accesibilidad) y 10 ficheros `.spec.ts`, pero:
  1. `cloudbuild.yaml` del front **no ejecutaba ningún test** — solo construía la imagen Docker y desplegaba.
  2. Los 10 `.spec.ts` son el boilerplate por defecto de `ng generate` (`expect(component).toBeTruthy()`), sin cobertura real de lógica de negocio.

## Corrección aplicada

- `karma.conf.js`: nuevo `customLaunchers.ChromeHeadlessCI` (`--no-sandbox --disable-gpu`, necesario porque Cloud Build corre como root sin sandbox de Chrome disponible).
- `cloudbuild.yaml` (front): nuevo paso `ng test --watch=false --browsers=ChromeHeadlessCI` **antes** de construir la imagen — si los tests fallan, el pipeline se detiene y no se despliega nada.

## Pendiente (no resuelto en esta task, decisión de alcance aparte)

- La cobertura de tests sigue siendo la boilerplate por defecto — no se ha escrito nueva cobertura real de lógica de negocio (sería una task de fondo aparte, no una corrección puntual de esta verificación).

## Entregable / Evidencia

Este documento + `karma.conf.js` + `cloudbuild.yaml` (front).

## Pendiente antes de marcar como Hecho

- [ ] Verificación local de que `ng test --watch=false --browsers=ChromeHeadlessCI` pasa en verde
- [ ] Confirmar en un build real de Cloud Build que el nuevo paso se ejecuta y bloquea correctamente
- [ ] Validación del responsable del contrato
- [ ] Enlazar esta evidencia al issue #88
