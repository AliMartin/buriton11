<?php

namespace Drupal\buriton_editor_search\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a private keyword search independent of the public search index.
 */
final class EditorSearchForm extends FormBase {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityFieldManagerInterface $entityFieldManager,
    private readonly DateFormatterInterface $dateFormatter,
  ) {}

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager'),
      $container->get('date.formatter'),
    );
  }

  public function getFormId() {
    return 'buriton_editor_search';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $parameters = $this->getRequest()->query->all();
    $keywords = is_string($parameters['q'] ?? NULL) ? mb_substr(trim($parameters['q']), 0, 200) : '';
    $published = ($parameters['published'] ?? '1') === '1';
    $unpublished = ($parameters['unpublished'] ?? '1') === '1';

    $form['q'] = [
      '#type' => 'search',
      '#title' => $this->t('Keywords'),
      '#description' => $this->t('Search titles and text fields, including story and event descriptions. All words must match; they can appear in different fields.'),
      '#default_value' => $keywords,
      '#maxlength' => 200,
      '#required' => TRUE,
    ];
    $form['published'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Published'),
      '#default_value' => $published,
    ];
    $form['unpublished'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Unpublished'),
      '#default_value' => $unpublished,
    ];
    $form['actions'] = ['#type' => 'actions', '#weight' => 1];
    $form['actions']['submit'] = ['#type' => 'submit', '#value' => $this->t('Search')];
    // Never share unpublished search results through render or page caches.
    $form['#cache'] = ['max-age' => 0];

    if ($keywords === '') {
      return $form;
    }
    if (!$published && !$unpublished) {
      $form['results'] = ['#markup' => $this->t('Select Published or Unpublished to see results.'), '#weight' => 10];
      return $form;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $query = $storage->getQuery()->accessCheck(TRUE);
    if ($published !== $unpublished) {
      $query->condition('status', $published ? 1 : 0);
    }

    $columns = ['title'];
    foreach ($this->entityFieldManager->getFieldStorageDefinitions('node') as $name => $definition) {
      if ($definition->isBaseField()) {
        continue;
      }
      if (in_array($definition->getType(), ['string', 'string_long', 'text', 'text_long', 'text_with_summary'], TRUE)) {
        $columns[] = $name . '.value';
        if ($definition->getType() === 'text_with_summary') {
          $columns[] = $name . '.summary';
        }
      }
    }
    foreach (preg_split('/\s+/u', $keywords, -1, PREG_SPLIT_NO_EMPTY) as $word) {
      $matches = $query->orConditionGroup();
      foreach ($columns as $column) {
        // Entity queries escape LIKE wildcards and parameterize these values.
        $matches->condition($column, $word, 'CONTAINS');
      }
      $query->condition($matches);
    }
    $ids = $query->sort('changed', 'DESC')->sort('nid', 'DESC')->pager(25)->execute();
    $rows = [];
    $types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($storage->loadMultiple($ids) as $node) {
      // Preserve entity access checks even for users granted this permission.
      if (!$node->access('view', $this->currentUser())) {
        continue;
      }
      $edit = $node->access('update', $this->currentUser()) ? $node->toLink($this->t('Edit'), 'edit-form') : '';
      $rows[] = [
        $node->toLink(),
        $types[$node->bundle()]->label(),
        $node->isPublished() ? $this->t('Published') : $this->t('Unpublished'),
        $this->dateFormatter->format($node->getChangedTime(), 'short'),
        $edit,
      ];
    }
    $form['results'] = [
      '#type' => 'table',
      '#weight' => 10,
      '#caption' => $this->t('Search results'),
      '#header' => [$this->t('Title'), $this->t('Content type'), $this->t('Status'), $this->t('Updated'), $this->t('Operations')],
      '#rows' => $rows,
      '#empty' => $this->t('No matching content found.'),
    ];
    $form['pager'] = ['#type' => 'pager', '#weight' => 20];
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('buriton_editor_search.search', [], ['query' => [
      'q' => trim($form_state->getValue('q')),
      'published' => $form_state->getValue('published') ? '1' : '0',
      'unpublished' => $form_state->getValue('unpublished') ? '1' : '0',
    ]]);
  }

}
