<?php
/** CLI-only configuration change summary; reports key paths, never values/secrets. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
use Symfony\Component\Yaml\Yaml;
function changes($before, $after, string $path = ''): array {
  if ($before === $after) { return []; }
  if (!is_array($before) || !is_array($after)) { return [$path]; }
  $keys = array_unique(array_merge(array_keys($before), array_keys($after)));
  $result = [];
  foreach ($keys as $key) {
    $p = $path === '' ? (string) $key : $path . '.' . $key;
    if (!array_key_exists($key, $before) || !array_key_exists($key, $after)) { $result[] = $p; }
    else { $result = array_merge($result, changes($before[$key], $after[$key], $p)); }
  }
  return $result;
}
function compareDirs(string $a, string $b): array {
  $names = array_unique(array_merge(array_map('basename', glob($a . '/*.yml')), array_map('basename', glob($b . '/*.yml'))));
  sort($names); $result = [];
  foreach ($names as $name) {
    $old = is_file($a . '/' . $name) ? Yaml::parseFile($a . '/' . $name) : null;
    $new = is_file($b . '/' . $name) ? Yaml::parseFile($b . '/' . $name) : null;
    if ($old !== $new) { $result[$name] = ['status' => $old === null ? 'added' : ($new === null ? 'removed' : 'changed'), 'keys' => changes($old, $new)]; }
  }
  return $result;
}
$result = [
  'initial_export_vs_root_sync' => compareDirs('sync', 'tmp/upgrade-initial-config'),
  'initial_to_final' => compareDirs('tmp/upgrade-initial-config', 'tmp/upgrade-final-config'),
  'pre11_to_final' => compareDirs('tmp/upgrade-pre11-config', 'tmp/upgrade-final-config'),
  'role_permission_changes' => [],
];
foreach (glob('tmp/upgrade-final-config/user.role.*.yml') as $file) {
  $old = Yaml::parseFile('tmp/upgrade-initial-config/' . basename($file));
  $new = Yaml::parseFile($file);
  $a = $old['permissions'] ?? []; $b = $new['permissions'] ?? [];
  if ($a !== $b) { $result['role_permission_changes'][basename($file)] = ['added' => array_values(array_diff($b, $a)), 'removed' => array_values(array_diff($a, $b))]; }
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), "\n";
