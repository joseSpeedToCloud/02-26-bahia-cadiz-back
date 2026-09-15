# TASK-038 — Módulo de Agenda de Eventos (REQ-069)

**Categoría:** CMS / Backend / Frontend
**Requisito origen:** REQ-069 (issue #90 / Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.3)
**Prioridad:** Alta
**Story Points:** 3
**Labels:** backend, frontend, cms, eventos, agenda
**Estado:** Hecho (con matiz — ver "Vista de agenda")

## Descripción técnica

Módulo de Agenda de Eventos para gestión, visualización y difusión de actividades turísticas, culturales, sociales y deportivas de la Mancomunidad, con consulta pública y administración por gestores autorizados.

## Verificación realizada

### Backend (Drupal) — content type `evento`

Confirmado en `config/sync/node.type.evento.yml:5-7`. Campos reales (`config/sync/field.field.node.evento.*.yml`):

- `field_evento_categoria` (entity_reference → taxonomy_term, vocabulario `categoria_evento`)
- `field_evento_etiquetas` (taxonomy, requerido — "Etiquetas semánticas UNE 178503")
- `field_evento_municipio` (taxonomy, requerido)
- `field_evento_fecha_inicio` / `field_evento_fecha_fin` (datetime, inicio requerido)
- `field_evento_direccion` + latitud/longitud
- `field_evento_imagen`, `field_evento_video`, `field_evento_audio`, `field_evento_foto360`
- `field_evento_precio`, `field_evento_organizador`, `field_evento_enlace`, `field_evento_accesible`, `field_evento_valoracion`

### Backend — permisos ("consulta pública" vs "gestores autorizados")

Confirmado en `config/sync/user.role.*.yml`:
- `anonymous`: solo `access content` (lectura, sin cuenta) → consulta pública.
- `editor_municipio`: `create/edit/delete any evento content` → gestor autorizado.
- `administrator`: acceso total.
- `content_editor` y `usuario_portal` NO tienen permisos sobre `evento` (confirma que solo el rol de gestor municipal administra eventos, no cualquier editor ni el ciudadano registrado).

### Difusión

- `bahia_cadiz_api.module:33-51` — `hook_node_insert`/`hook_node_update` envían notificación push automática cuando un evento se publica (construido en TASK-036/REQ-066).
- `evento-detalle.page.html:29` — botón de compartir en redes (REQ-061).
- `evento-detalle.page.ts:64-95` — JSON-LD `schema.org/Event` para SEO/difusión externa.

### Frontend

- `eventos.page.ts`: listado con buscador de texto (título/municipio) + filtro jerárquico por categoría (`seleccionarCategoria`, `aplicarFiltro`), destacando (no ocultando) eventos afines a los "temas de interés" del usuario si hay sesión iniciada.
- `evento-detalle.page.ts`: ficha completa (imagen, foto 360°, vídeo/audio, fecha/hora, dirección, accesibilidad, valoración, organizador, etiquetas) + botón "Añadir al calendario" (exportación iCal individual, `IcalService`).

### Categorías reales (taxonomía `categoria_evento`) — verificado contra producción

Consulta directa a `GET /jsonapi/taxonomy_term/categoria_evento` en producción (no simulable en local: son datos de contenido, no configuración exportada). Antes de esta task existían: `Cultura`, `Cultura y ocio`, `Deporte`, `Ferias y fiestas`, `Gastronomia`, `Gastronomia y tradicion`, `Musica`, `Ocio familiar`.

Cubrían "Cultural" y "Deportiva" del pliego, pero **no existían los términos "Turístico" ni "Social"** literalmente. Se han creado ambos vía API (autenticado como administrador, módulo `basic_auth`):
- `POST /jsonapi/taxonomy_term/categoria_evento` → `"Social"` (creado, tid nuevo).
- `POST /jsonapi/taxonomy_term/categoria_evento` → `"Turístico"` (creado, tid nuevo).

Verificado tras la creación: los 8 términos previos siguen intactos + los 2 nuevos, con la tilde correctamente codificada en UTF-8.

## Vista de agenda — decisión de alcance

El pliego usa el nombre "Agenda de Eventos", pero el texto del requisito no exige literalmente una vista de calendario visual — pide "gestión, visualización y difusión ... con consulta pública y administración por gestores autorizados", todo lo cual está cubierto por el listado filtrable + ficha de detalle + exportación iCal ya existentes. Se decide no construir un componente de calendario mensual/semanal nuevo, por no ser parte del texto literal del requisito. Si el cliente considera imprescindible una vista de calendario visual, sería una ampliación de alcance a tratar aparte.

## Pendiente antes de marcar como Hecho

- [ ] Confirmar visualmente en el portal que las nuevas categorías "Turístico" y "Social" aparecen ya en el filtro de la página de Eventos (puede requerir invalidar caché de Drupal/CDN).
- [ ] Validación del responsable del contrato.
- [ ] Enlazar esta evidencia al issue #90.
