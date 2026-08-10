# TASK-F01 — Desarrollo y puesta en producción del portal web (agenda, playas, movilidad)

**Categoría:** Diseño/UX
**Requisito origen:** REQ-038 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Alta
**Story Points:** 8 *(estimado)*
**Labels:** frontend, angular, ionic, portal
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2 (Portal web), cita literal: *"Desarrollo y puesta en producción de un portal web donde se visualizarán todos los contenidos relacionados con la agenda cultural, playas y movilidad sostenible de la Mancomunidad."*

A diferencia de los REQ anteriores (backend/CMS), esta es un **entregable** de frontend: el propio portal, no un requisito técnico interno del CMS.

## Auditoría real (2026-08-07)

El portal existe y está desarrollado — repo `02-26-web-front` (Angular 20 / Ionic 8), consumiendo el JSON:API real del backend headless. Buena parte de este trabajo se hizo a lo largo de la sesión bajo tasks nominalmente "backend" (TASK-019, TASK-020, TASK-021 del panel de backend, etiquetadas también `frontend`), sin que existiera hasta ahora un panel propio de frontend que documentara el entregable como tal. Se crea este panel (`panel/README-frontend.md`) y esta primera task para corregirlo.

**Verificado real contra producción** (no solo el código, la URL pública):

```
curl https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app/            -> 200
curl https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app/tabs/eventos -> 200
curl https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app/tabs/playas  -> 200
curl https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app/tabs/mapa    -> 200
curl https://gcp-crs-back-02-26-bh-729653171489.europe-southwest1.run.app/jsonapi/node/evento -> 200
```

## Cobertura real por módulo

- **Agenda cultural**: listado y ficha de detalle de eventos (`/tabs/eventos`, `/evento/:id`), con fecha/hora, categoría, organizador, accesibilidad, compartir en RRSS, foto 360°, etiquetas semánticas, y botón "Añadir al calendario" (iCal).
- **Playas**: listado y ficha (`/tabs/playas`, `/playa/:id`) con estado del mar, bandera, ocupación, servicios, accesibilidad, datos estructurados JSON-LD (`schema:Beach`) para SEO.
- **Movilidad sostenible**: mapa interactivo unificado (`/tabs/mapa`, deck.gl — ver nota de licencia más abajo) con las 6 geometrías (playa/evento/ruta/transporte/aparcamiento/aviso de tráfico), fichas de detalle de ruta (con mini-mapa, track GPX real o aproximado, puntos de interés cercanos vía Overpass API), transporte y aparcamiento.

**Nota de licencia** (relevante para este entregable en concreto): el mapa se migró de Leaflet a `deck.gl` (MIT) durante la sesión — Leaflet es BSD-2-Clause, fuera de la lista de licencias permitidas por el contrato (PPT cláusula 14). Detalle completo en `panel/TASK-021-backend-geolocalizacion-mapas.md`.

## Completado 2026-08-07: selector de idioma + empaquetado móvil

Los dos puntos que se habían dejado explícitamente sin confirmar se implementaron y verificaron de verdad.

### 1. Selector de idioma en el portal

No existía ningún mecanismo — el backend soporta ES/EN desde TASK-016, pero el front nunca pedía la traducción ni ofrecía cambiar de idioma.

**Trabajo realizado:**
- `LanguageService` (nuevo, `src/app/services/language.service.ts`): guarda la preferencia (`localStorage`), expone el prefijo real de ruta JSON:API (`''` para es, `/en` para en — el mismo esquema de TASK-016) y un pequeño diccionario para las cadenas propias de la interfaz.
- `JsonApiService` actualizado para anteponer ese prefijo a **todas** las peticiones — así el contenido que devuelve el backend (títulos, descripciones) viene en el idioma elegido, no solo la interfaz.
- `LanguageSwitchComponent` (botón flotante "ES"/"EN", visible en toda la app — montado una sola vez en `app.component.html`, fuera del router-outlet).
- `TraducirPipe` + diccionario aplicado a la barra de pestañas (Inicio/Eventos/Playas/Rutas/Mapa) y a los botones comunes "Añadir al calendario" y "Descargar track".
- **Alcance honesto, no sobre-afirmado**: se tradujeron la navegación y los CTAs comunes a varias fichas, no el 100% de las cadenas de cada página individual (títulos de cabecera como "Playas", etiquetas de campo como "Bandera verde") — eso queda como trabajo futuro si se decide ampliar.

**Verificado real con Playwright contra el backend local, no solo "debería funcionar":**
- Antes: tabs en español (`Inicio, Eventos, Playas, Rutas, Mapa`), botón ofrece "EN".
- Clic → recarga → tabs en inglés (`Home, Events, Beaches, Routes, Map`), botón ahora ofrece "ES".
- **Contenido real del backend traducido**: la página de Playas muestra "Victoria Beach" (la traducción real creada en TASK-016) y ya NO muestra "Playa de la Victoria" — confirma que el prefijo `/en/jsonapi/...` llega de verdad a la petición, no es solo la interfaz.
- Sin errores de consola.

### 2. Empaquetado como app móvil nativa (Capacitor)

Auditoría real: Capacitor **ya estaba** en `package.json` (`@capacitor/android`, `@capacitor/ios`, `@capacitor/core`, etc.) y los proyectos nativos `android/`/`ios/` ya existían en el repo con `capacitor.config.ts` configurado (`appId: com.mancomunidadbahiacadiz.portal`) — trabajo de una sesión anterior, nunca verificado con un build real.

**Verificado real esta vez, de punta a punta:**
- `ng build --configuration production` + `npx cap sync android` — sincroniza el build web más reciente con el proyecto nativo sin errores.
- **Compilación real de un APK** con Gradle (Android SDK/Android Studio ya instalados en la máquina de desarrollo): `BUILD SUCCESSFUL`, `android/app/build/outputs/apk/debug/app-debug.apk` generado (~6 MB). Metadatos confirmados con `aapt dump badging`: `package: name='com.mancomunidadbahiacadiz.portal' versionName='1.0'`, permisos `INTERNET`/`VIBRATE`.
- **Instalado y ejecutado en un emulador Android real** (no solo compilado): `adb install` → `Success`; la app queda como actividad resumida en primer plano (`topResumedActivity=...MainActivity`); **captura de pantalla real** de la app corriendo mostrando la navegación real (Agenda de Eventos, Playas, Movilidad Sostenible, Mapa) con la tab bar nativa funcionando.
- Confirma literalmente el PPT ap. 4.2: *"El portal web se podrá empaquetar y ejecutar como aplicación híbrida en dispositivos Android e iOS sin necesidad de reescritura del código fuente principal."* — mismo código Angular/Ionic, sin ningún cambio, corriendo nativo.
- **Limitación honesta**: solo se verificó Android. iOS requiere macOS/Xcode para compilar un `.ipa` real, no disponible en esta máquina de desarrollo (Windows) — la configuración (`ios/`, `capacitor.config.ts`) existe y Capacitor la soporta por diseño, pero no se ha compilado ni ejecutado un build de iOS real. Queda pendiente de verificar en una máquina con Xcode.
- Entorno de prueba limpiado tras la verificación: app desinstalada del emulador, emulador apagado.

## Entregable / Evidencia

Portal real en producción, verificado con curl (200 en home + las 3 rutas de agenda/playas/movilidad) y con Playwright en múltiples puntos de esta sesión contra el backend real (capturas y comprobaciones de consola sin errores en `/tabs/mapa`, `/evento/:id`, `/playa/:id`, `/ruta/:id`, `/aparcamiento/:id`, `/transporte/:id`). Selector de idioma verificado end-to-end (UI + contenido real traducido). Empaquetado Android verificado con un APK real compilado, instalado y ejecutado en emulador. iOS pendiente de verificar (requiere macOS).

Pendiente exclusivamente de validación por el responsable del contrato.
