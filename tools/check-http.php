<?php

/**
 * CLI-only, anonymous HTTP GET smoke checks; no submissions or logins.
 * Run with THEME_QA_BASE_URL and drush php:script tools/check-http.php.
 */
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit;
}
$base = rtrim(getenv('THEME_QA_BASE_URL') ?: 'https://buriton9.ddev.site', '/');
$paths = ['/', '/user/login', '/user/password', '/search/node?keys=Buriton', '/events', '/county-councillor-reports', '/aggregator/rss', '/parish-council-minutes', '/parish-council-agendas', '/parish-council-finances', '/parish-council-financial-reports', '/planning-committee-minutes', '/district-councillor-reports', '/rss.xml'];
foreach (['contact', 'contact_buriton_village_hall'] as $id) {
  $form = \Drupal::entityTypeManager()->getStorage('webform')->load($id);
  if (!$form) {
    throw new \RuntimeException('Missing contact form: ' . $id);
  }
  $paths[] = $form->toUrl()->toString();
}
$results = [];
foreach ($paths as $path) {
  $response = \Drupal::httpClient()->get($base . $path, ['http_errors' => FALSE, 'allow_redirects' => FALSE, 'timeout' => 30]);
  $status = $response->getStatusCode();
  $body = (string) $response->getBody();
  if ($status !== 200 || $body === '' || str_contains($body, 'The website encountered an unexpected error')) {
    throw new \RuntimeException('HTTP check failed for ' . $path . ': ' . $status);
  }
  if ($path === '/aggregator/rss') {
    $previous = libxml_use_internal_errors(TRUE);
    if (simplexml_load_string($body, \SimpleXMLElement::class, LIBXML_NONET) === FALSE) {
      throw new \RuntimeException('Aggregator RSS is not well-formed XML.');
    }
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
  }
  $results[$path] = ['status' => $status, 'bytes' => strlen($body), 'content_type' => $response->getHeaderLine('Content-Type')];
}
echo json_encode(['core' => \Drupal::VERSION, 'routes' => $results, 'method' => 'anonymous GET only', 'browser_execution' => FALSE], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
