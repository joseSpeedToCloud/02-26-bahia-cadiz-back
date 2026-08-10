# TASK-F02 — Visualización real de los 4 módulos (gestor de contenidos, agenda, playas, movilidad)

**Categoría:** Diseño/UX
**Requisito origen:** REQ-041 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.2)
**Prioridad:** Media
**Story Points:** 2 *(estimado — verificación, no desarrollo nuevo)*
**Labels:** frontend, portal, verificacion
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.2, cita literal: *"Mostrará información proveniente de los módulos: gestor de contenidos, agenda de eventos, playas y movilidad sostenible."*

Igual que [TASK-026](TASK-026-solucion-llave-en-mano-produccion.md) (REQ-040), no es desarrollo nuevo: es la confirmación literal, en vivo contra producción, de que el portal muestra contenido real proveniente de los 4 módulos citados — no solo que las páginas existan, sino que cada una tiene datos reales renderizados.

## Verificación real (2026-08-10) — navegador real contra producción

Se navegó con Playwright a cada módulo en `https://gcp-crs-front-02-26-bh-729653171489.europe-southwest1.run.app`, capturando el texto real renderizado y comprobando ausencia de errores de consola:

| Módulo | Ruta | Contenido real detectado | Errores |
|---|---|---|---|
| **Gestor de contenidos** (portada) | `/tabs/home` | Tarjetas de los 4 módulos con su descripción real ("Agenda de Eventos — Cultura, ocio y actividades de los municipios", "Playas — Estado del mar, ocupación y banderas en tiempo real", etc.) | Ninguno |
| **Agenda de eventos** | `/tabs/eventos` | 2 eventos reales con categoría, fecha, municipio y precio: "Feria de Mayo de Cádiz" (15 MAY, Cádiz, Gratuito), "Concierto en la Playa de la Victoria" (25 JUL, Cádiz, 10 EUR) | Ninguno |
| **Playas** | `/tabs/playas` | 2 playas reales con bandera/ocupación: "Playa de la Barrosa" (Chiclana, bandera verde, ocupación alta), "Playa de la Victoria" (Cádiz, bandera verde, ocupación media) | Ninguno |
| **Movilidad sostenible** (rutas) | `/tabs/rutas` | 2 rutas reales con distancia/duración/dificultad: "Vía Verde de la Bahía de Cádiz" (24,5 km, 90 min, dificultad media), "Sendero litoral Rota - El Puerto de Santa María" (12,3 km, 180 min, dificultad baja) | Ninguno |
| **Movilidad sostenible** (mapa unificado) | `/tabs/mapa` | Captura de pantalla real: marcadores de playas (verde), eventos (azul oscuro), transporte/aparcamiento (naranja) y avisos de tráfico (rojo) simultáneos sobre el mapa real de la Bahía de Cádiz, más el trazado de una ruta | Ninguno |

Nota técnica: en el mapa los marcadores son capas WebGL de `deck.gl` (`<canvas>`), no elementos `ion-card`/`ion-item` del DOM — un primer intento de contar "items" con selectores de DOM dio 0 falsamente; se confirmó visualmente con captura de pantalla en su lugar (mismo criterio ya aplicado en TASK-025 para el mismo tipo de capa).

## Entregable / Evidencia

Cada uno de los 4 módulos citados literalmente por el pliego muestra información real, en producción, verificado con navegador real (no simulado, no local) y sin errores de consola en ninguno. Capturas de pantalla generadas durante la verificación disponibles en el historial de esta sesión.

Pendiente exclusivamente de validación por el responsable del contrato.
