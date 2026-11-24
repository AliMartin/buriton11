<?php
/** CLI-only replay helper: apply migration deltas while retaining pre-existing export edits. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
use Symfony\Component\Yaml\Yaml;
$conflicts = [];
function mergeUpgrade($old, $new, $current, string $path) {
  global $conflicts;
  if ($old === $new) { return $current; }
  if ($current === $old || $current === $new) { return $new; }
  if (is_array($old) && is_array($new) && is_array($current) && (!array_is_list($old) || !array_is_list($new))) {
    $result = $current;
    foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $key) {
      $p = $path . '.' . $key;
      if (!array_key_exists($key, $new)) {
        if (array_key_exists($key, $current) && $current[$key] === $old[$key]) { unset($result[$key]); }
        elseif (array_key_exists($key, $current)) { $conflicts[] = $p; }
      }
      elseif (!array_key_exists($key, $old)) {
        if (!array_key_exists($key, $current) || $current[$key] === $new[$key]) { $result[$key] = $new[$key]; }
        else { $conflicts[] = $p; }
      }
      else { $result[$key] = mergeUpgrade($old[$key], $new[$key], $current[$key] ?? null, $p); }
    }
    return $result;
  }
  $conflicts[] = $path;
  return $current;
}
$initial = 'tmp/upgrade-initial-config'; $final = 'tmp/upgrade-final-config';
$names = array_unique(array_merge(array_map('basename', glob($initial . '/*.yml')), array_map('basename', glob($final . '/*.yml'))));
$written = []; $removed = [];
foreach ($names as $name) {
  $a = is_file($initial . '/' . $name) ? Yaml::parseFile($initial . '/' . $name) : null;
  $b = is_file($final . '/' . $name) ? Yaml::parseFile($final . '/' . $name) : null;
  $file = 'sync/' . $name;
  $c = is_file($file) ? Yaml::parseFile($file) : null;
  if ($a === $b) { continue; }
  if ($b === null && $c === $a) { unlink($file); $removed[] = $name; continue; }
  $merged = mergeUpgrade($a, $b, $c, $name);
  if ($merged !== $c) {
    file_put_contents($file, Yaml::dump($merged, 30, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK));
    $written[] = $name;
  }
}
echo json_encode(['written' => $written, 'removed' => $removed, 'conflicts_retained_for_review' => $conflicts], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
