<?php
/** CLI compatibility scan using Upgrade Status services (also works without Drush command discovery). */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$analyzer = \Drupal::service('upgrade_status.deprecation_analyzer');
$analyzer->initEnvironment();
$themes = \Drupal::service('extension.list.theme')->getList();
$results = [];
foreach (['zircon', 'buriton5'] as $name) {
  $analyzer->analyze($themes[$name]);
  $results[$name] = \Drupal::service('upgrade_status.result_storage')->get($name);
  if (!$results[$name]) { throw new \RuntimeException('No scan result for ' . $name); }
}
echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
