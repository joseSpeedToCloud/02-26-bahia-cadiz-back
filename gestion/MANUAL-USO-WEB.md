# Manual de uso de la Plataforma Digital Bahía de Cádiz

**Proyecto:** Expediente 312/2026
**Requisito origen:** REQ-015 (apartado "manual de uso de la web con descripción de los módulos")
**Fecha:** 2026-07-30
**Alcance:** portal público (Angular/Ionic, web + PWA + app Android/iOS empaquetada) y gestión de contenidos en el CMS (Drupal), descrito a partir de la funcionalidad real implementada.

---

## 1. Portal público — módulos para el usuario final

El portal se organiza en 4 secciones accesibles desde la pantalla de Inicio, más un mapa unificado:

### 1.1 Inicio
Pantalla de acceso rápido a las 4 secciones siguientes.

### 1.2 Agenda de Eventos
Listado de eventos culturales, de ocio y actividades de los municipios de la Bahía. Cada evento muestra: descripción, fecha/hora de inicio y fin, municipio, categoría, lugar/dirección, imagen, precio, enlace a más información/entradas, si es accesible, y organizador. Actualizable manualmente deslizando hacia abajo (pull-to-refresh).

### 1.3 Playas
Listado de playas con información en tiempo real: estado del mar, bandera (verde/amarilla/roja), nivel de ocupación, longitud, si es accesible para movilidad reducida, y servicios disponibles (socorrismo, duchas, etc.). Incluye imagen y geolocalización de cada playa.

### 1.4 Movilidad Sostenible
Rutas ciclistas, senderistas, peatonales y accesibles para moverse por la Bahía sin coche: distancia, duración estimada, dificultad, municipios que recorre, y track descargable (GPX/KML) para GPS. Incluye también información de transporte público (autobús metropolitano CTAN, marítimo, Cercanías Renfe) y aparcamientos (públicos, zona azul/naranja ORA, disuasorios).

### 1.5 Mapa
Todas las playas, rutas, paradas de transporte y aparcamientos localizados en un único mapa interactivo (Leaflet), con capas activables/desactivables por tipo de recurso.

### Instalación como app
El portal es una PWA instalable desde el navegador (sin pasar por tienda de apps) y también está empaquetado como app nativa Android/iOS (Capacitor) — mismo código, sin funcionalidad distinta.

## 2. Gestión de contenidos — CMS (Drupal)

### 2.1 Tipos de contenido disponibles

| Tipo | Para qué sirve |
|---|---|
| Evento | Agenda de eventos |
| Playa | Estado y datos de playas |
| Ruta | Rutas de movilidad sostenible |
| Transporte | Líneas/servicios de transporte público |
| Aparcamiento | Zonas de aparcamiento |
| Aviso de tráfico | Cortes, obras, desvíos e incidencias de circulación |
| Parte meteorológico | Previsión/estado meteorológico por municipio y fecha |

Cada tipo tiene sus propios campos (ver la ficha de creación de cada uno en el CMS — los campos obligatorios están marcados con asterisco).

### 2.2 Multisitio por municipio

Cada nodo de contenido (evento, playa, ruta, etc.) se asocia a uno o varios municipios mediante el campo correspondiente (p. ej. `Municipio`/`Municipios servidos`). Un editor municipal solo puede crear/editar contenido de su propio municipio — el sistema restringe automáticamente la edición según el municipio asignado al usuario.

### 2.3 Flujo básico de publicación

1. Acceder al CMS con el usuario y contraseña facilitados por el administrador.
2. Ir a "Contenido" → "Añadir contenido" → elegir el tipo (Evento, Playa, etc.).
3. Rellenar los campos obligatorios (marcados con asterisco) y los opcionales que apliquen.
4. Guardar. El contenido aparece automáticamente en el portal público en la sección correspondiente (no requiere ningún paso adicional de "publicación al portal" — el portal lee directamente del CMS).

## 3. Soporte

Para incidencias de uso, contactar con el equipo técnico (SpeedToCloud) según el canal acordado con la Mancomunidad. Para dudas sobre contenido de un municipio concreto, contactar con el editor municipal correspondiente.

---

Este manual describe la funcionalidad real implementada a fecha de este documento. Si se añaden nuevos módulos o tipos de contenido, debe actualizarse.

Validado por: _pendiente — responsable del contrato (Mancomunidad)_
