<?php

/**
 * Aprovisiona los tipos de contenido base de la Plataforma Digital Bahia de Cadiz.
 *
 * Cubre TASK-015 (tipos de contenido: Evento, Playa, Ruta) y sienta la base de
 * TASK-018 (taxonomias/navegacion) con los vocabularios compartidos (Municipio,
 * Categoria de Evento, Servicios de Playa, Etiquetas Semanticas UNE 178503).
 *
 * Idempotente: se puede volver a ejecutar sin duplicar nada (comprueba existencia
 * antes de crear). Tras ejecutarlo, exportar con `drush config:export` para que
 * quede versionado en config/sync.
 *
 * Uso: drush php:script scripts/provision-content-types.php
 */

use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\NodeType;
use Drupal\pathauto\Entity\PathautoPattern;

// ────────────────────────────────────────────────────────────────────────────
// Helpers idempotentes
// ────────────────────────────────────────────────────────────────────────────

function ensure_vocabulary(string $vid, string $name, string $description = ''): void {
  if (!Vocabulary::load($vid)) {
    Vocabulary::create(['vid' => $vid, 'name' => $name, 'description' => $description])->save();
    echo "  [vocab] creado: $vid ($name)" . PHP_EOL;
  }
}

function ensure_term(string $vid, string $name): void {
  $existing = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
    ->loadByProperties(['vid' => $vid, 'name' => $name]);
  if (empty($existing)) {
    Term::create(['vid' => $vid, 'name' => $name])->save();
    echo "    [term] creado: $name" . PHP_EOL;
  }
}

function ensure_field_storage(string $entity_type, string $field_name, string $type, array $settings = [], int $cardinality = 1): void {
  if (!FieldStorageConfig::loadByName($entity_type, $field_name)) {
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'type' => $type,
      'settings' => $settings,
      'cardinality' => $cardinality,
    ])->save();
    echo "  [field-storage] creado: $field_name ($type)" . PHP_EOL;
  }
}

function ensure_field(string $entity_type, string $bundle, string $field_name, string $label, array $settings = [], bool $required = FALSE, string $description = ''): void {
  if (!FieldConfig::loadByName($entity_type, $bundle, $field_name)) {
    FieldConfig::create([
      'field_name' => $field_name,
      'entity_type' => $entity_type,
      'bundle' => $bundle,
      'label' => $label,
      'settings' => $settings,
      'required' => $required,
      'description' => $description,
    ])->save();
    echo "    [field] $bundle.$field_name -> $label" . PHP_EOL;
  }
}

function ensure_content_type(string $type, string $name, string $description = ''): void {
  if (!NodeType::load($type)) {
    NodeType::create([
      'type' => $type,
      'name' => $name,
      'description' => $description,
      'new_revision' => TRUE,
      'display_submitted' => FALSE,
    ])->save();
    echo "[content-type] creado: $type ($name)" . PHP_EOL;
  }
}

/**
 * Coloca un campo en el formulario y en la vista por defecto con el widget/
 * formateador indicados, y fija el orden (weight) segun el momento en que se
 * llama (se incrementa un contador estatico por bundle).
 */
function place_field(string $bundle, string $field_name, string $widget_type, array $widget_settings, string $formatter_type, array $formatter_settings = []): void {
  static $weights = [];
  $weights[$bundle] = ($weights[$bundle] ?? 0) + 1;
  $weight = $weights[$bundle];

  \Drupal::service('entity_display.repository')
    ->getFormDisplay('node', $bundle, 'default')
    ->setComponent($field_name, ['type' => $widget_type, 'weight' => $weight, 'settings' => $widget_settings])
    ->save();

  \Drupal::service('entity_display.repository')
    ->getViewDisplay('node', $bundle, 'default')
    ->setComponent($field_name, ['type' => $formatter_type, 'weight' => $weight, 'label' => 'above', 'settings' => $formatter_settings])
    ->save();
}

function taxonomy_ref_settings(string $vid): array {
  return [
    'target_type' => 'taxonomy_term',
    'handler' => 'default:taxonomy_term',
    'handler_settings' => ['target_bundles' => [$vid => $vid], 'sort' => ['field' => 'name', 'direction' => 'asc']],
  ];
}

// ────────────────────────────────────────────────────────────────────────────
// 1. Vocabularios de taxonomia (TASK-018)
// ────────────────────────────────────────────────────────────────────────────

echo "== Vocabularios ==" . PHP_EOL;

ensure_vocabulary('municipio', 'Municipio', 'Municipios miembros de la Mancomunidad de la Bahia de Cadiz.');
foreach (['Cadiz', 'San Fernando', 'Puerto Real', 'El Puerto de Santa Maria', 'Chiclana de la Frontera', 'Rota'] as $t) {
  ensure_term('municipio', $t);
}

ensure_vocabulary('categoria_evento', 'Categoria de evento', 'Clasificacion de la Agenda de Eventos.');
foreach (['Cultura', 'Musica', 'Deporte', 'Ocio familiar', 'Gastronomia', 'Ferias y fiestas'] as $t) {
  ensure_term('categoria_evento', $t);
}

ensure_vocabulary('servicios_playa', 'Servicios de playa', 'Servicios y equipamientos disponibles en cada playa.');
foreach (['Socorrismo', 'Duchas', 'Aparcamiento', 'Chiringuito', 'Acceso adaptado', 'Papeleras', 'Wifi'] as $t) {
  ensure_term('servicios_playa', $t);
}

ensure_vocabulary('etiquetas_semanticas', 'Etiquetas semanticas (UNE 178503)', 'Etiquetado libre alineado con la ontologia de destinos turisticos inteligentes UNE 178503. Los editores anaden terminos segun lo necesiten.');

// ────────────────────────────────────────────────────────────────────────────
// 2. Tipo de contenido: Evento (Agenda de Eventos)
// ────────────────────────────────────────────────────────────────────────────

echo "== Evento ==" . PHP_EOL;
ensure_content_type('evento', 'Evento', 'Elemento de la Agenda de Eventos: cultura, ocio y actividades de los municipios.');

ensure_field_storage('node', 'field_evento_descripcion', 'text_long');
ensure_field_storage('node', 'field_evento_fecha_inicio', 'datetime', ['datetime_type' => 'datetime']);
ensure_field_storage('node', 'field_evento_fecha_fin', 'datetime', ['datetime_type' => 'datetime']);
ensure_field_storage('node', 'field_evento_municipio', 'entity_reference', taxonomy_ref_settings('municipio'));
ensure_field_storage('node', 'field_evento_categoria', 'entity_reference', taxonomy_ref_settings('categoria_evento'), -1);
ensure_field_storage('node', 'field_evento_direccion', 'string', ['max_length' => 255]);
ensure_field_storage('node', 'field_evento_latitud', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_evento_longitud', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_evento_imagen', 'image');
ensure_field_storage('node', 'field_evento_precio', 'string', ['max_length' => 64]);
ensure_field_storage('node', 'field_evento_enlace', 'link');
ensure_field_storage('node', 'field_evento_accesible', 'boolean');
ensure_field_storage('node', 'field_evento_organizador', 'string', ['max_length' => 255]);
ensure_field_storage('node', 'field_evento_etiquetas', 'entity_reference', taxonomy_ref_settings('etiquetas_semanticas'), -1);

ensure_field('node', 'evento', 'field_evento_descripcion', 'Descripcion', [], TRUE);
ensure_field('node', 'evento', 'field_evento_fecha_inicio', 'Fecha y hora de inicio', [], TRUE);
ensure_field('node', 'evento', 'field_evento_fecha_fin', 'Fecha y hora de fin');
ensure_field('node', 'evento', 'field_evento_municipio', 'Municipio', [], TRUE);
ensure_field('node', 'evento', 'field_evento_categoria', 'Categoria');
ensure_field('node', 'evento', 'field_evento_direccion', 'Direccion / lugar de celebracion');
ensure_field('node', 'evento', 'field_evento_latitud', 'Latitud', [], FALSE, 'Coordenada GPS (WGS84) para integracion GIS (WMS/WFS/GeoJSON).');
ensure_field('node', 'evento', 'field_evento_longitud', 'Longitud');
ensure_field('node', 'evento', 'field_evento_imagen', 'Imagen destacada');
ensure_field('node', 'evento', 'field_evento_precio', 'Precio', [], FALSE, "Ej: 'Gratuito', '10 EUR', 'Desde 5 EUR'.");
ensure_field('node', 'evento', 'field_evento_enlace', 'Enlace (entradas / mas informacion)');
ensure_field('node', 'evento', 'field_evento_accesible', 'Evento accesible', [], FALSE, 'Cumple criterios de accesibilidad (WCAG 2.1 AA / RD 1112/2018).');
ensure_field('node', 'evento', 'field_evento_organizador', 'Organizador');
ensure_field('node', 'evento', 'field_evento_etiquetas', 'Etiquetas semanticas (UNE 178503)');

place_field('evento', 'field_evento_descripcion', 'text_textarea', [], 'text_default');
place_field('evento', 'field_evento_fecha_inicio', 'datetime_default', [], 'datetime_default');
place_field('evento', 'field_evento_fecha_fin', 'datetime_default', [], 'datetime_default');
place_field('evento', 'field_evento_municipio', 'options_select', [], 'entity_reference_label');
place_field('evento', 'field_evento_categoria', 'options_buttons', [], 'entity_reference_label');
place_field('evento', 'field_evento_direccion', 'string_textfield', [], 'string');
place_field('evento', 'field_evento_latitud', 'number', [], 'number_decimal');
place_field('evento', 'field_evento_longitud', 'number', [], 'number_decimal');
place_field('evento', 'field_evento_imagen', 'image_image', [], 'image');
place_field('evento', 'field_evento_precio', 'string_textfield', [], 'string');
place_field('evento', 'field_evento_enlace', 'link_default', [], 'link');
place_field('evento', 'field_evento_accesible', 'boolean_checkbox', [], 'boolean');
place_field('evento', 'field_evento_organizador', 'string_textfield', [], 'string');
place_field('evento', 'field_evento_etiquetas', 'entity_reference_autocomplete_tags', [], 'entity_reference_label');

// ────────────────────────────────────────────────────────────────────────────
// 3. Tipo de contenido: Playa
// ────────────────────────────────────────────────────────────────────────────

echo "== Playa ==" . PHP_EOL;
ensure_content_type('playa', 'Playa', 'Informacion en tiempo real de una playa: estado del mar, ocupacion, banderas y accesibilidad.');

ensure_field_storage('node', 'field_playa_descripcion', 'text_long');
ensure_field_storage('node', 'field_playa_municipio', 'entity_reference', taxonomy_ref_settings('municipio'));
ensure_field_storage('node', 'field_playa_estado_mar', 'list_string', ['allowed_values' => ['tranquilo' => 'Tranquilo', 'moderado' => 'Moderado', 'agitado' => 'Agitado']]);
ensure_field_storage('node', 'field_playa_bandera', 'list_string', ['allowed_values' => ['verde' => 'Verde', 'amarilla' => 'Amarilla', 'roja' => 'Roja']]);
ensure_field_storage('node', 'field_playa_ocupacion', 'list_string', ['allowed_values' => ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']]);
ensure_field_storage('node', 'field_playa_longitud_m', 'integer');
ensure_field_storage('node', 'field_playa_accesible', 'boolean');
ensure_field_storage('node', 'field_playa_servicios', 'entity_reference', taxonomy_ref_settings('servicios_playa'), -1);
ensure_field_storage('node', 'field_playa_latitud', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_playa_longitud', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_playa_imagen', 'image');
ensure_field_storage('node', 'field_playa_etiquetas', 'entity_reference', taxonomy_ref_settings('etiquetas_semanticas'), -1);

ensure_field('node', 'playa', 'field_playa_descripcion', 'Descripcion', [], TRUE);
ensure_field('node', 'playa', 'field_playa_municipio', 'Municipio', [], TRUE);
ensure_field('node', 'playa', 'field_playa_estado_mar', 'Estado del mar');
ensure_field('node', 'playa', 'field_playa_bandera', 'Bandera');
ensure_field('node', 'playa', 'field_playa_ocupacion', 'Ocupacion');
ensure_field('node', 'playa', 'field_playa_longitud_m', 'Longitud de la playa (metros)');
ensure_field('node', 'playa', 'field_playa_accesible', 'Accesible para movilidad reducida', [], FALSE, 'Cumple criterios de accesibilidad (WCAG 2.1 AA, ap. 4.4 del PPT).');
ensure_field('node', 'playa', 'field_playa_servicios', 'Servicios disponibles');
ensure_field('node', 'playa', 'field_playa_latitud', 'Latitud', [], FALSE, 'Coordenada GPS (WGS84) para integracion GIS (WMS/WFS/GeoJSON) y exposicion JSON-LD/schema.org (TASK-019).');
ensure_field('node', 'playa', 'field_playa_longitud', 'Longitud');
ensure_field('node', 'playa', 'field_playa_imagen', 'Imagen destacada');
ensure_field('node', 'playa', 'field_playa_etiquetas', 'Etiquetas semanticas (UNE 178503)');

place_field('playa', 'field_playa_descripcion', 'text_textarea', [], 'text_default');
place_field('playa', 'field_playa_municipio', 'options_select', [], 'entity_reference_label');
place_field('playa', 'field_playa_estado_mar', 'options_select', [], 'list_default');
place_field('playa', 'field_playa_bandera', 'options_select', [], 'list_default');
place_field('playa', 'field_playa_ocupacion', 'options_select', [], 'list_default');
place_field('playa', 'field_playa_longitud_m', 'number', [], 'number_integer');
place_field('playa', 'field_playa_accesible', 'boolean_checkbox', [], 'boolean');
place_field('playa', 'field_playa_servicios', 'options_buttons', [], 'entity_reference_label');
place_field('playa', 'field_playa_latitud', 'number', [], 'number_decimal');
place_field('playa', 'field_playa_longitud', 'number', [], 'number_decimal');
place_field('playa', 'field_playa_imagen', 'image_image', [], 'image');
place_field('playa', 'field_playa_etiquetas', 'entity_reference_autocomplete_tags', [], 'entity_reference_label');

// ────────────────────────────────────────────────────────────────────────────
// 4. Tipo de contenido: Ruta (Movilidad Sostenible)
// ────────────────────────────────────────────────────────────────────────────

echo "== Ruta ==" . PHP_EOL;
ensure_content_type('ruta', 'Ruta', 'Ruta de movilidad sostenible (ciclista, senderista o accesible) de la Bahia de Cadiz.');

ensure_field_storage('node', 'field_ruta_descripcion', 'text_long');
ensure_field_storage('node', 'field_ruta_tipo', 'list_string', ['allowed_values' => ['ciclista' => 'Ciclista', 'senderismo' => 'Senderismo', 'accesible' => 'Accesible', 'urbana' => 'Urbana']]);
ensure_field_storage('node', 'field_ruta_municipios', 'entity_reference', taxonomy_ref_settings('municipio'), -1);
ensure_field_storage('node', 'field_ruta_distancia_km', 'decimal', ['precision' => 6, 'scale' => 2]);
ensure_field_storage('node', 'field_ruta_duracion_min', 'integer');
ensure_field_storage('node', 'field_ruta_dificultad', 'list_string', ['allowed_values' => ['baja' => 'Baja', 'media' => 'Media', 'alta' => 'Alta']]);
ensure_field_storage('node', 'field_ruta_track', 'file', ['uri_scheme' => 'public', 'file_extensions' => 'gpx kml']);
ensure_field_storage('node', 'field_ruta_lat_inicio', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_ruta_lng_inicio', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_ruta_lat_fin', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_ruta_lng_fin', 'decimal', ['precision' => 10, 'scale' => 6]);
ensure_field_storage('node', 'field_ruta_imagen', 'image');
ensure_field_storage('node', 'field_ruta_etiquetas', 'entity_reference', taxonomy_ref_settings('etiquetas_semanticas'), -1);

ensure_field('node', 'ruta', 'field_ruta_descripcion', 'Descripcion', [], TRUE);
ensure_field('node', 'ruta', 'field_ruta_tipo', 'Tipo de ruta', [], TRUE);
ensure_field('node', 'ruta', 'field_ruta_municipios', 'Municipios que recorre');
ensure_field('node', 'ruta', 'field_ruta_distancia_km', 'Distancia (km)');
ensure_field('node', 'ruta', 'field_ruta_duracion_min', 'Duracion estimada (min)');
ensure_field('node', 'ruta', 'field_ruta_dificultad', 'Dificultad');
ensure_field('node', 'ruta', 'field_ruta_track', 'Track (GPX/KML)', [], FALSE, 'Fichero de la ruta para su integracion GIS.');
ensure_field('node', 'ruta', 'field_ruta_lat_inicio', 'Latitud punto de inicio');
ensure_field('node', 'ruta', 'field_ruta_lng_inicio', 'Longitud punto de inicio');
ensure_field('node', 'ruta', 'field_ruta_lat_fin', 'Latitud punto de fin');
ensure_field('node', 'ruta', 'field_ruta_lng_fin', 'Longitud punto de fin');
ensure_field('node', 'ruta', 'field_ruta_imagen', 'Imagen destacada');
ensure_field('node', 'ruta', 'field_ruta_etiquetas', 'Etiquetas semanticas (UNE 178503)');

place_field('ruta', 'field_ruta_descripcion', 'text_textarea', [], 'text_default');
place_field('ruta', 'field_ruta_tipo', 'options_select', [], 'list_default');
place_field('ruta', 'field_ruta_municipios', 'options_buttons', [], 'entity_reference_label');
place_field('ruta', 'field_ruta_distancia_km', 'number', [], 'number_decimal');
place_field('ruta', 'field_ruta_duracion_min', 'number', [], 'number_integer');
place_field('ruta', 'field_ruta_dificultad', 'options_select', [], 'list_default');
place_field('ruta', 'field_ruta_track', 'file_generic', [], 'file_default');
place_field('ruta', 'field_ruta_lat_inicio', 'number', [], 'number_decimal');
place_field('ruta', 'field_ruta_lng_inicio', 'number', [], 'number_decimal');
place_field('ruta', 'field_ruta_lat_fin', 'number', [], 'number_decimal');
place_field('ruta', 'field_ruta_lng_fin', 'number', [], 'number_decimal');
place_field('ruta', 'field_ruta_imagen', 'image_image', [], 'image');
place_field('ruta', 'field_ruta_etiquetas', 'entity_reference_autocomplete_tags', [], 'entity_reference_label');

// ────────────────────────────────────────────────────────────────────────────
// 5. Patrones de alias de URL (Pathauto)
//
// Sin patron, Pathauto lanza un aviso de deprecacion en cada guardado de nodo
// de estos bundles (PathautoGenerator::getPatternByEntity() con patron nulo).
// ────────────────────────────────────────────────────────────────────────────

echo "== Patrones de alias (Pathauto) ==" . PHP_EOL;

$url_patterns = [
  'evento' => ['label' => 'Evento', 'path' => '/eventos/[node:title]'],
  'playa' => ['label' => 'Playa', 'path' => '/playas/[node:title]'],
  'ruta' => ['label' => 'Ruta', 'path' => '/rutas/[node:title]'],
];

foreach ($url_patterns as $bundle => $info) {
  if (PathautoPattern::load($bundle)) {
    continue;
  }
  PathautoPattern::create([
    'id' => $bundle,
    'label' => $info['label'],
    'type' => 'canonical_entities:node',
    'pattern' => $info['path'],
    'selection_criteria' => [
      'bundle_condition' => [
        'id' => 'entity_bundle:node',
        'bundles' => [$bundle => $bundle],
        'negate' => FALSE,
        'context_mapping' => ['node' => 'node'],
      ],
    ],
    'selection_logic' => 'and',
    'weight' => 0,
    'relationships' => [],
  ])->save();
  echo "  [pathauto] patron creado: $bundle -> {$info['path']}" . PHP_EOL;
}

echo PHP_EOL . "Listo. Revisa /admin/structure/types y /admin/structure/taxonomy, y luego ejecuta:" . PHP_EOL;
echo "  drush config:export --yes" . PHP_EOL;
