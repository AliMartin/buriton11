<?php

/**
 * Render the status report as administrator, then GET its local CSS/JS assets.
 * CLI only: drush php:script tools/check-admin-assets.php.
 * No login, form submission, configuration change or inline settings export.
 */
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit;
}
$base = rtrim(getenv('THEME_QA_BASE_URL') ?: 'https://buriton9.ddev.site', '/');
$warnings = [];
set_error_handler(static function ($level, $message) use (&$warnings) {
  if (($level & error_reporting()) && in_array($level, [E_WARNING, E_USER_WARNING, E_DEPRECATED, E_USER_DEPRECATED], TRUE)) {
    $warnings[] = $message;
    return TRUE;
  }
  return FALSE;
});
$switcher = \Drupal::service('account_switcher');
$switcher->switchTo(\Drupal\user\Entity\User::load(1));
try {
  $theme = \Drupal::service('theme.initialization')->getActiveThemeByName('claro');
  \Drupal::theme()->setActiveTheme($theme);
  $request = \Symfony\Component\HttpFoundation\Request::create($base . '/admin/reports/status');
  $response = \Drupal::service('http_kernel')->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
  if ($response->getStatusCode() !== 200) {
    throw new \RuntimeException('Status report failed to render.');
  }
  $previous = libxml_use_internal_errors(TRUE);
  $dom = new \DOMDocument();
  $dom->loadHTML($response->getContent());
  libxml_clear_errors();
  libxml_use_internal_errors($previous);
  $xpath = new \DOMXPath($dom);
  $urls = [];
  $libraries = [];
  foreach ($xpath->query('//script[@src]/@src | //link[@rel="stylesheet"]/@href') as $attribute) {
    $url = $attribute->nodeValue;
    $parts = parse_url($url);
    if (isset($parts['host']) && $parts['host'] !== parse_url($base, PHP_URL_HOST)) {
      continue;
    }
    if (!isset($parts['host'])) {
      $url = $base . '/' . ltrim($url, '/');
    }
    parse_str($parts['query'] ?? '', $query);
    foreach (['include', 'exclude'] as $key) {
      if (isset($query[$key])) {
        $libraries = array_merge($libraries, explode(',', \Drupal\Component\Utility\UrlHelper::uncompressQueryParameter($query[$key])));
      }
    }
    $urls[$url] = TRUE;
  }
  foreach ($xpath->query('//script[@data-drupal-selector="drupal-settings-json"]') as $settings) {
    $data = json_decode($settings->textContent, TRUE, 512, JSON_THROW_ON_ERROR);
    if (!empty($data['ajaxPageState']['libraries'])) {
      $libraries = array_merge($libraries, explode(',', \Drupal\Component\Utility\UrlHelper::uncompressQueryParameter($data['ajaxPageState']['libraries'])));
    }
  }
  $libraries = array_values(array_unique(array_filter($libraries)));
  sort($libraries);
  $obsolete = array_values(array_filter($libraries, static fn ($name) => str_starts_with($name, 'tour/')));
  if ($obsolete) {
    throw new \RuntimeException('Current status report still requests Tour libraries.');
  }
  if (!$urls) {
    throw new \RuntimeException('No local status report assets were found.');
  }
  $assets = [];
  foreach (array_keys($urls) as $url) {
    $asset = \Drupal::httpClient()->get($url, ['http_errors' => FALSE, 'allow_redirects' => FALSE, 'timeout' => 30]);
    $body = (string) $asset->getBody();
    $path = parse_url($url, PHP_URL_PATH);
    if ($asset->getStatusCode() !== 200 || $body === '' || str_contains($body, 'theme is missing from the file system: tour') || str_contains($body, 'Passing null to parameter #1 ($path)')) {
      throw new \RuntimeException('Status report asset failed: ' . $path . ' (' . $asset->getStatusCode() . ')');
    }
    $assets[] = ['path' => $path, 'status' => $asset->getStatusCode(), 'bytes' => strlen($body), 'content_type' => $asset->getHeaderLine('Content-Type')];
  }
  if ($warnings) {
    throw new \RuntimeException('Status report warnings: ' . json_encode($warnings));
  }
  echo json_encode([
    'core' => \Drupal::VERSION,
    'status_report' => 200,
    'theme' => \Drupal::theme()->getActiveTheme()->getName(),
    'tour_installed' => \Drupal::moduleHandler()->moduleExists('tour'),
    'obsolete_tour_libraries' => $obsolete,
    'libraries' => $libraries,
    'local_assets' => $assets,
    'warnings' => $warnings,
    'limit' => 'Fresh server-rendered page and HTTP assets; an existing browser tab/cache is not inspected.',
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
}
finally {
  $switcher->switchBack();
  restore_error_handler();
}
