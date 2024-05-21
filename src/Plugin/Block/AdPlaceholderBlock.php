<?php

namespace Drupal\advertisement\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a banner ad placeholder block.
 *
 * @Block(
 *   id = "advertisement",
 *   admin_label = @Translation("Ad Placeholder"),
 *   category = @Translation("Advertisement")
 * )
 */
class AdPlaceholderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'category' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['category'] = [
      '#type' => 'text',
      '#title' => $this->t('Category'),
      '#default_value' => $this->configuration['category'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['category'] = $form_state->getValue('category');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $html_id = $this->getPluginDefinition()['id'];

    $build['content'] = [
      '#type' => 'html_tag',
      '#tag' => 'ad-content',
      '#attributes' => [
        'id' => Html::getUniqueId($html_id),
      ],
      '#attached' => [
        'library' => ['advertisement/advertisement'],
      ],
    ];

    // Add cache metadata.
    $build['#cache'] = [
      'max-age' => $this->getCacheMaxAge(),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    $cacheConfig = \Drupal::config('system.performance')->get('cache');

    return $cacheConfig['page']['max_age'];
  }

}
