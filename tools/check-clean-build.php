<?php
/** CLI-only compare patched generated packages to an independent clean build. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$build = '/tmp/buriton-clean-build-20261124';
$results = [];
foreach (['modules/custom/tb_megamenu', 'themes/custom/bootstrap5', 'libraries/jquery.cycle'] as $path) {
  $files = [];
  $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DRUPAL_ROOT . '/' . $path, FilesystemIterator::SKIP_DOTS));
  foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getFilename() === '.DS_Store') { continue; }
    $relative = substr($file->getPathname(), strlen(DRUPAL_ROOT . '/') );
    if (!is_file($build . '/' . $relative) || hash_file('sha256', $file->getPathname()) !== hash_file('sha256', $build . '/' . $relative)) {
      throw new RuntimeException('Clean build mismatch: ' . $relative);
    }
    $files[] = $relative;
  }
  $results[$path] = ['matching_files' => count($files)];
}
foreach (['autoload_runtime.php', 'vendor/autoload_runtime.php', 'core/lib/Drupal.php'] as $path) {
  if (!is_file($build . '/' . $path)) { throw new RuntimeException('Missing clean build runtime file: ' . $path); }
}
echo json_encode(['clean_build' => $build, 'packages' => $results, 'runtime_scaffold_present' => true], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
