<?php

/**
 * Read-only regression checks for the contributed-module update.
 * Run through Drush. No forms are submitted and no menu configuration is saved.
 */
if (PHP_SAPI !== 'cli') {
  http_response_code(403);
  exit;
}

$warnings = [];
set_error_handler(function ($level, $message, $file, $line) use (&$warnings) {
  if (($level & error_reporting()) && in_array($level, [E_WARNING, E_USER_WARNING, E_DEPRECATED, E_USER_DEPRECATED])) {
    $warnings[] = "$message ($file:$line)";
    return TRUE;
  }
  return FALSE;
});
$switcher = \Drupal::service('account_switcher');
$switcher->switchTo(new \Drupal\Core\Session\AnonymousUserSession());
$result = ['core' => \Drupal::VERSION, 'related_views' => [], 'admin_routes' => []];
$storage = \Drupal::service('config.storage');
$config_hashes = function () use ($storage) {
  $hashes = [];
  foreach ($storage->listAll() as $name) {
    $hashes[$name] = hash('sha256', serialize($storage->read($name)));
  }
  return $hashes;
};
$before_config = $config_hashes();
try {
  // Use real published, tagged content as contexts for the site's two views.
  $query = \Drupal::database()->select('taxonomy_index', 't');
  $query->join('node_field_data', 'n', 'n.nid = t.nid');
  $contexts = $query->fields('n', ['nid'])->condition('n.status', 1)
    ->condition('n.type', ['event', 'news'], 'IN')->distinct()
    ->orderBy('n.nid', 'DESC')->range(0, 4)->execute()->fetchCol();
  if (!$contexts) {
    throw new RuntimeException('No published tagged content available for related-content checks.');
  }
  foreach (['news_block', 'events_block'] as $name) {
    foreach ($contexts as $nid) {
      $view = \Drupal\views\Views::getView($name);
      $view->setDisplay('block_1');
      $view->display_handler->overrideOption('cache', ['type' => 'none']);
      $view->setArguments([(string) $nid]);
      $view->execute();
      $rows = array_map(fn($row) => (int) $row->_entity->id(), $view->result);
      $build = \Drupal::service('renderer')->executeInRenderContext(
        new \Drupal\Core\Render\RenderContext(), fn() => $view->render()
      );
      $html = (string) \Drupal::service('renderer')->renderInIsolation($build);
      $result['related_views'][$name][(string) $nid] = ['node_ids' => $rows, 'rendered_bytes' => strlen($html)];
      $view->destroy();
    }
  }

  $switcher->switchBack();
  $switcher->switchTo(\Drupal\user\Entity\User::load(1));
  $theme = \Drupal::service('theme.initialization')->getActiveThemeByName('claro');
  foreach (['fontawesome.admin_settings', 'entity.tb_megamenu.collection'] as $route) {
    \Drupal::theme()->setActiveTheme($theme);
    $path = \Drupal::urlGenerator()->generateFromRoute($route);
    $request = \Symfony\Component\HttpFoundation\Request::create('https://buriton11.ddev.site' . $path);
    $response = \Drupal::service('http_kernel')->handle($request, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
    $result['admin_routes'][$route] = $response->getStatusCode();
    if ($response->getStatusCode() !== 200) {
      throw new RuntimeException("Failed to render $route");
    }
  }
  // Exercise the actual JSON load callback without calling its save branch.
  $menu_names = $storage->listAll('tb_megamenu.menu_config.');
  if (!$menu_names) {
    throw new RuntimeException('No configured mega menu available to test.');
  }
  $menu = $storage->read($menu_names[0]);
  $controller = \Drupal\tb_megamenu\Controller\TBMegaMenuAdminController::create(\Drupal::getContainer());
  $request = \Symfony\Component\HttpFoundation\Request::create(
    'https://buriton11.ddev.site/admin/structure/tb-megamenu/save/' . $menu['id'],
    'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'],
    json_encode(['action' => 'load', 'menu_name' => $menu['menu'], 'theme' => $menu['theme']], JSON_THROW_ON_ERROR)
  );
  $response = \Drupal::service('renderer')->executeInRenderContext(
    new \Drupal\Core\Render\RenderContext(), fn() => $controller->saveConfiguration($request)
  );
  $result['mega_menu_load'] = ['status' => $response->getStatusCode(), 'rendered_bytes' => strlen($response->getContent())];
  if ($response->getStatusCode() !== 200 || !$response->getContent()) {
    throw new RuntimeException('Mega menu JSON load callback failed.');
  }
  $result['active_config_unchanged'] = $before_config === $config_hashes();
  if (!$result['active_config_unchanged']) {
    throw new RuntimeException('Read-only checks unexpectedly changed active configuration.');
  }
  $result['warnings'] = $warnings;
  echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
  if ($warnings && !getenv('CONTRIB_QA_ALLOW_WARNINGS')) {
    throw new RuntimeException('Contributed module regression check reported warnings.');
  }
}
finally {
  restore_error_handler();
  $switcher->switchBack();
}
