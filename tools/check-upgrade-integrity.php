<?php
/** CLI-only content/file integrity collector; output is private upgrade evidence. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$db = \Drupal::database();
if (getenv('UPGRADE_BASELINE_DATABASE') === 'buriton_upgrade9_verify' && getenv('IS_DDEV_PROJECT') === 'true') {
  $options = $db->getConnectionOptions();
  $options['database'] = 'buriton_upgrade9_verify';
  $options['username'] = 'root';
  $options['password'] = 'root'; // DDEV's local-only database credentials.
  \Drupal\Core\Database\Database::addConnectionInfo('upgrade_baseline', 'default', $options);
  $db = \Drupal\Core\Database\Database::getConnection('default', 'upgrade_baseline');
}
$result = ['tables' => [], 'files' => []];
$tables = $db->schema()->findTables('%');
sort($tables);
foreach ($tables as $table) {
  if (!preg_match('/^(node($|_)|users($|_)|user__|media($|_)|taxonomy_term($|_)|webform_submission($|_)|file_managed$)/', $table)) { continue; }
  $rows = [];
  foreach ($db->select($table, 't')->fields('t')->execute() as $row) {
    $record = (array) $row; ksort($record);
    $rows[] = hash('sha256', serialize($record));
  }
  sort($rows);
  $result['tables'][$table] = ['count' => count($rows), 'sha256' => hash('sha256', implode("\n", $rows))];
}
foreach ($db->select('file_managed', 'f')->fields('f', ['fid', 'uri'])->orderBy('fid')->execute() as $file) {
  $exists = is_file($file->uri);
  $result['files'][$file->fid] = ['uri' => $file->uri, 'exists' => $exists, 'sha256' => $exists ? hash_file('sha256', $file->uri) : null];
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
