# TASK-023 — Grafo de conocimiento / interoperabilidad semántica (schema.org)

**Categoría:** CMS
**Requisito origen:** REQ-034 (Checklist_PlataformaDigitalBahiaCadiz.xlsx — PPT ap. 4.1)
**Prioridad:** Alta
**Story Points:** 5 *(estimado)*
**Labels:** backend, cms, drupal, une-178503, semantica
**Estado:** Hecho

## Descripción técnica

PPT ap. 4.1, cita literal: *"El modelo de datos estará alineado con la norma UNE 178503, incorporando **grafos de conocimiento, taxonomías, ontologías y vocabularios controlados** para garantizar la interoperabilidad con **sistemas de IA, buscadores semánticos y plataformas de turismo inteligente**."*

Distinto de TASK-018 (taxonomías jerárquicas/categorías) y TASK-019 (etiquetas semánticas): aquellas cubren el modelo de datos interno de Drupal, pero sin exponerlo con un vocabulario formal reconocible por sistemas externos. Esta task cubre justo esa interoperabilidad semántica hacia afuera — "grafo de conocimiento" y "ontologías" en sentido literal (web semántica / linked data), no solo estructura de datos interna.

## Auditoría real (2026-08-07)

No existía ningún mapeo RDF ni vocabulario schema.org en el proyecto — `config/sync/` no tenía ninguna entidad `rdf.mapping.*`. El JSON-LD de Playas (TASK-005, `schema:Beach`) es una implementación puntual en el *frontend* Angular para un solo content type; no cubre el resto ni existe a nivel del propio modelo de datos del CMS.

**Hallazgo real durante la implementación**: en Drupal 11, el módulo `rdf` **ya no viene en core** (se movió a paquete contrib separado, `drupal/rdf` 4.0.0, GPL-2.0-or-later — sigue cumpliendo la cláusula 14 del PPT). Documentado por si se repite la confusión en otro entorno: intentar `drush pm:enable rdf` sin haberlo añadido antes a `composer.json` falla con "Unable to install modules rdf due to missing modules rdf."

## Trabajo realizado

- Añadido `drupal/rdf` (GPL-2.0-or-later) a `composer.json`/`composer.lock`, habilitado en `entrypoint.sh` (idempotente, ambas ramas de instalación/actualización).
- `scripts/provision-rdf-mappings.php` (nuevo, idempotente, mismo patrón `ensure_*` del resto del proyecto): mapea cada tipo de contenido y cada vocabulario controlado a un tipo real de **schema.org**:

| Bundle | Tipo schema.org |
|---|---|
| `node.evento` | `schema:Event` |
| `node.playa` | `schema:Beach` |
| `node.ruta` | `schema:TouristAttraction` (no existe un tipo "Trail" estable en schema.org) |
| `node.transporte` | `schema:TransitStation` |
| `node.aparcamiento` | `schema:ParkingFacility` |
| `taxonomy_term.etiquetas_semanticas` | `schema:DefinedTerm` |
| `taxonomy_term.categoria_evento` | `schema:DefinedTerm` |
| `taxonomy_term.municipio` | `schema:DefinedTerm` |
| `taxonomy_term.servicios_playa` | `schema:DefinedTerm` |

Cada campo relevante (título, descripción, imagen, fechas de evento, coordenadas) se mapea a su propiedad schema.org correspondiente (`schema:name`, `schema:description`, `schema:image`, `schema:startDate`/`endDate`, `schema:latitude`/`longitude`, `schema:url`).

## Entregable / Evidencia

Verificado real (no solo "debería funcionar"), tras `docker compose up -d --build` desde cero:

- Los 9 mapeos confirmados creados en el log de aprovisionamiento (`[rdf] node.evento -> schema:Event`, etc.).
- **RDFa real en el HTML publicado**: `GET /node/3` (Playa de la Victoria) devuelve en el marcado `typeof="schema:Beach"` en el contenedor del nodo, y `property="schema:name"`, `property="schema:description"`, `property="schema:latitude"`, `property="schema:longitude"` en los campos correspondientes — confirmado con Playwright leyendo el DOM real, no solo inspeccionando código.
- Config exportada y copiada al repo (`config/sync/rdf.mapping.*.yml`, más los mapeos por defecto que el propio módulo instala para `taxonomy_term.tags`/`user.user`).
- Nota de estado, no relacionada con este trabajo: el nodo de prueba estaba en `moderation_state: draft` en esta instalación local fresca (drift conocido de contenido de ejemplo entre reinicios del contenedor, ya documentado en tasks anteriores) — no afecta al RDFa, que se renderiza igual independientemente del estado de publicación; verificado con sesión de administrador.
- Entorno local levantado y desmontado durante la verificación.

Pendiente exclusivamente de validación por el responsable del contrato.
