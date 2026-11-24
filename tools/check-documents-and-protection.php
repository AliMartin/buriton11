<?php
/** CLI-only local-document and web-root protection checks. No form submissions. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$base = rtrim(getenv('THEME_QA_BASE_URL') ?: 'https://buriton9.ddev.site', '/');
$client = \Drupal::httpClient(); $result = ['documents' => [], 'protected_paths' => []];
foreach (\Drupal::database()->select('file_managed', 'f')->fields('f', ['fid','uri'])->orderBy('fid', 'DESC')->execute() as $file) {
  if (!str_starts_with($file->uri, 'public://') || !preg_match('/\.(pdf|docx?)$/i', $file->uri) || !is_file($file->uri)) { continue; }
  $url = \Drupal::service('file_url_generator')->generateAbsoluteString($file->uri);
  $response = $client->get($url, ['http_errors' => false, 'timeout' => 30]);
  $bytes = (string) $response->getBody();
  if ($response->getStatusCode() !== 200 || hash('sha256', $bytes) !== hash_file('sha256', $file->uri)) { throw new RuntimeException('Document download mismatch for file ID ' . $file->fid); }
  $result['documents'][] = ['fid' => (int) $file->fid, 'status' => 200, 'bytes' => strlen($bytes), 'sha256_matches_disk' => true];
  if (count($result['documents']) === 3) { break; }
}
if (count($result['documents']) !== 3) { throw new RuntimeException('Fewer than three existing documents were verified.'); }
$paths = ['/sync/core.extension.yml','/_upgrade-poc/buriton11-poc/composer.json','/docs/upgrade-progress.md','/tools/check-http.php','/tmp/upgrade-final-config/core.extension.yml'];
$privateFile = null;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DRUPAL_ROOT . '/private-files', FilesystemIterator::SKIP_DOTS)) as $file) {
  if ($file->isFile() && !str_starts_with($file->getFilename(), '.')) { $privateFile = $file->getPathname(); break; }
}
if (!$privateFile) { throw new RuntimeException('No real private file found to test direct HTTP protection.'); }
$relative = substr($privateFile, strlen(DRUPAL_ROOT));
$paths[] = implode('/', array_map('rawurlencode', explode('/', $relative)));
foreach ($paths as $path) {
  $response = $client->head($base . $path, ['http_errors' => false, 'allow_redirects' => false, 'timeout' => 30]);
  $status = $response->getStatusCode();
  if (!in_array($status, [403,404], true)) { throw new RuntimeException('Protected path exposed: ' . $path . ' (' . $status . ')'); }
  $result['protected_paths'][str_starts_with($path, '/private-files/') ? '/private-files/[existing file]' : $path] = $status;
}
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
