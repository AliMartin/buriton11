<?php

/**
 * Read-only upgrade baseline. Run from the project root with:
 * ddev drush php:script docs/drupal11/check-baseline.php
 * Contains counts and fingerprints, never passwords, mail addresses or content.
 */
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit;
}

$db = \Drupal::database();
$storage = \Drupal::service('config.storage');
$extensions = \Drupal::config('core.extension');
$module_info = \Drupal::service('extension.list.module')->getList();
$theme_info = \Drupal::service('extension.list.theme')->getList();
$result = [
  'core' => \Drupal::VERSION,
  'php' => PHP_VERSION,
  'database_version' => $db->version(),
  'themes' => \Drupal::config('system.theme')->getRawData(),
  'modules' => [],
  'installed_themes' => [],
  'counts' => [],
  'fingerprints' => [],
];
foreach ($extensions->get('module') as $name => $weight) {
  $info = $module_info[$name]->info ?? [];
  $result['modules'][$name] = [
    'version' => $info['version'] ?? NULL,
    'core_version_requirement' => $info['core_version_requirement'] ?? NULL,
  ];
}
foreach ($extensions->get('theme') as $name => $weight) {
  $info = $theme_info[$name]->info ?? [];
  $result['installed_themes'][$name] = [
    'version' => $info['version'] ?? NULL,
    'base_theme' => $info['base theme'] ?? NULL,
    'core_version_requirement' => $info['core_version_requirement'] ?? NULL,
  ];
}
foreach (['node', 'users', 'file_managed', 'taxonomy_term_data', 'media', 'webform_submission', 'aggregator_feed', 'aggregator_item'] as $table) {
  if ($db->schema()->tableExists($table)) {
    $result['counts'][$table] = (int) $db->select($table, 't')->countQuery()->execute()->fetchField();
  }
}
// Hash ordered records so comparison detects changes without exposing content.
// Exclude volatile Aggregator fetch timestamps, but retain feed definitions/items.
$fingerprint_fields = [
  'node' => ['nid', 'uuid', 'type'],
  'users' => ['uid', 'uuid'],
  'file_managed' => ['fid', 'uuid', 'uri', 'filesize'],
  'aggregator_feed' => ['fid', 'uuid', 'title', 'url', 'refresh', 'checked'],
  'aggregator_item' => ['iid', 'fid', 'title', 'link', 'author', 'description', 'timestamp', 'guid'],
];
foreach ($fingerprint_fields as $table => $fields) {
  if (!$db->schema()->tableExists($table)) {
    continue;
  }
  $fields = array_values(array_filter($fields, fn($field) => $db->schema()->fieldExists($table, $field)));
  if ($table === 'aggregator_feed') {
    $fields = array_values(array_diff($fields, ['checked']));
  }
  $hash = hash_init('sha256');
  foreach ($db->select($table, 't')->fields('t', $fields)->orderBy($fields[0])->execute() as $row) {
    hash_update($hash, json_encode((array) $row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . "\n");
  }
  $result['fingerprints'][$table] = ['fields' => $fields, 'sha256' => hash_final($hash)];
}
$result['legacy_password_accounts'] = (int) $db->select('users_field_data', 'u')->condition('pass', '$S$%', 'LIKE')->countQuery()->execute()->fetchField();
$result['content_types'] = [];
foreach ($storage->listAll('node.type.') as $name) {
  $result['content_types'][] = substr($name, strlen('node.type.'));
}
$result['editors'] = [];
foreach ($storage->listAll('editor.editor.') as $name) {
  $config = $storage->read($name);
  $result['editors'][$config['format']] = [
    'editor' => $config['editor'],
    'toolbar' => $config['settings']['toolbar']['items'] ?? [],
  ];
}
$result['filters'] = [];
foreach ($storage->listAll('filter.format.') as $name) {
  $config = $storage->read($name);
  $result['filters'][$config['format']] = array_keys(array_filter($config['filters'] ?? [], fn($filter) => !empty($filter['status'])));
}
$result['webforms'] = array_map(fn($name) => substr($name, strlen('webform.webform.')), $storage->listAll('webform.webform.'));
$result['rules_config_names'] = $storage->listAll('rules.');
$result['views'] = [];
foreach ($storage->listAll('views.view.') as $name) {
  $config = $storage->read($name);
  $paths = [];
  foreach ($config['display'] ?? [] as $display) {
    if (isset($display['display_options']['path'])) {
      $paths[] = $display['display_options']['path'];
    }
  }
  $result['views'][$config['id']] = ['enabled' => $config['status'] ?? NULL, 'paths' => $paths];
}
$result['active_config_count'] = count($storage->listAll());
$sync = new \Drupal\Core\Config\FileStorage(\Drupal\Core\Site\Settings::get('config_sync_directory'));
$result['sync_config_count'] = count($sync->listAll());
$result['config_sync_directory'] = \Drupal\Core\Site\Settings::get('config_sync_directory');
$result['private_file_path'] = \Drupal\Core\Site\Settings::get('file_private_path');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
