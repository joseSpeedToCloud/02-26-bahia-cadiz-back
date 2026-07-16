<?php

/**
 * Configuracion base del entorno (Plataforma Digital Bahia de Cadiz).
 *
 * Los valores sensibles se leen de variables de entorno (ver docker-compose.yml
 * y .env) para no versionar credenciales en Git. En produccion, las variables
 * las inyecta la infraestructura Terraform/Secret Manager (ver TASK-008).
 */

$databases['default']['default'] = [
  'database' => getenv('DRUPAL_DB_NAME') ?: 'drupal',
  'username' => getenv('DRUPAL_DB_USER') ?: 'drupal',
  'password' => getenv('DRUPAL_DB_PASSWORD') ?: 'drupal',
  'host' => getenv('DRUPAL_DB_HOST') ?: 'db',
  'port' => getenv('DRUPAL_DB_PORT') ?: '3306',
  'driver' => 'mysql',
  'prefix' => '',
  'collation' => 'utf8mb4_general_ci',
];

// Configuracion como codigo: toda la config de Drupal (tipos de contenido,
// roles, workflows, etc.) se exporta/importa desde config/sync, versionado en Git.
$settings['config_sync_directory'] = '../config/sync';

$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT') ?: 'CHANGE-ME-INSECURE-DEV-ONLY-SALT-DO-NOT-USE-IN-PROD';

$settings['trusted_host_patterns'] = [
  '^localhost$',
  '^127\.0\.0\.1$',
  '^drupal$',
];

$settings['file_public_path'] = 'sites/default/files';

// Sin esto, Drupal ignora sites/default/services.yml por completo (no se
// carga automaticamente por su sola presencia: hay que declararlo aqui).
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';

// Requisito ENS/RGPD (ver TASK-008): evitar exposicion de datos en despliegues no productivos.
$config['system.logging']['error_level'] = 'verbose';

if (file_exists(__DIR__ . '/settings.local.php')) {
  include __DIR__ . '/settings.local.php';
}
