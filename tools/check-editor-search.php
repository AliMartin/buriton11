<?php

/**
 * @file
 * Run with ddev drush php:script tools/check-editor-search.php.
 * Uses temporary content inside a rolled-back database transaction.
 */

use Drupal\buriton_editor_search\Form\EditorSearchForm;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AnonymousUserSession;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\Request;

$assert = static function (bool $condition, string $message): void {
  if (!$condition) {
    throw new RuntimeException($message);
  }
};
$access = \Drupal::service('access_manager');
$editor = User::create(['uid' => 999999999, 'name' => 'Search verification', 'roles' => ['website_editor']]);
$ordinary = User::create(['uid' => 999999998, 'name' => 'Ordinary verification', 'roles' => ['editor']]);
foreach ([new AnonymousUserSession(), $ordinary] as $account) {
  $assert(!$access->checkNamedRoute('buriton_editor_search.search', [], $account), 'Search must deny non-Website Editors.');
}
$assert($access->checkNamedRoute('buriton_editor_search.search', [], $editor), 'Website Editor must have search access.');
$link = \Drupal::service('plugin.manager.menu.link')->getDefinition('buriton_editor_search.search');
$assert($link['parent'] === 'system.admin_content', 'Search must be under Content.');

$transaction = \Drupal::database()->startTransaction();
$switcher = \Drupal::service('account_switcher');
$switcher->switchTo($editor);
try {
  $marker = 'BuritonSearchCheck' . bin2hex(random_bytes(6));
  foreach ([1, 0] as $status) {
    Node::create([
      'type' => 'news',
      'title' => "$marker title $status",
      'status' => $status,
      'created' => strtotime('2020-02-03 12:00:00 UTC'),
      'changed' => strtotime('2024-06-15 12:00:00 UTC'),
      'body' => ['value' => "BodyOnly$marker camping", 'format' => 'plain_text'],
    ])->save();
  }
  $build = static function (array $parameters): array {
    $stack = \Drupal::service('request_stack');
    $stack->push(Request::create('/admin/content/search-the-site', 'GET', $parameters));
    try {
      return EditorSearchForm::create(\Drupal::getContainer())->buildForm([], new FormState());
    }
    finally {
      $stack->pop();
    }
  };
  $form = $build([]);
  $assert($form['published']['#default_value'] && $form['unpublished']['#default_value'], 'Both statuses must default on.');
  foreach ([[$marker, '1', '1', 2], [$marker, '1', '0', 1], [$marker, '0', '1', 1], ["BodyOnly$marker", '1', '1', 2], ["$marker camping", '1', '1', 2], ["$marker missingword", '1', '1', 0]] as [$q, $published, $unpublished, $expected]) {
    $form = $build(compact('q', 'published', 'unpublished'));
    $rows = $form['results']['#rows'];
    $assert(count($rows) === $expected, "Wrong result count for $q ($published/$unpublished).");
    foreach ($rows as $row) {
      $assert($row[3] === '3 Feb 2020', 'Created must show the original date including its year.');
      $assert($row[4] === '15 Jun 2024', 'Updated must show the separate modification date including its year.');
    }
    if ($published !== $unpublished && $rows) {
      $assert((string) $rows[0][2] === ($published === '1' ? 'Published' : 'Unpublished'), 'Wrong publication status returned.');
    }
    $assert($form['#cache']['max-age'] === 0, 'Private results must not be cached.');
  }
  $form = $build(['q' => $marker, 'published' => '0', 'unpublished' => '0']);
  $assert(!isset($form['results']['#rows']), 'Both statuses off must not return content.');
  $form = [];
  $state = (new FormState())->setValues(['q' => ' camping ', 'published' => 0, 'unpublished' => 1]);
  EditorSearchForm::create(\Drupal::getContainer())->submitForm($form, $state);
  $assert($state->getRedirect()->getOption('query') === ['q' => 'camping', 'published' => '0', 'unpublished' => '1'], 'Search must preserve filters in the URL.');
  echo "PASS: role access, Content menu, defaults, title/body keywords, multiple words, status filters, empty results, cache policy and submit redirect.\n";
}
finally {
  $transaction->rollBack();
  $switcher->switchBack();
}
