<?php

namespace Drupal\advertisement\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a banner ad placeholder block.
 *
 * @Block(
 *   id = "advertisement",
 *   admin_label = @Translation("Ad Placeholder"),
 *   category = @Translation("Advetisement")
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
    // $form['category'] = BaseFieldDefinition::create('text')
    //   ->setLabel(new TranslatableMarkup('Category'))
    //   ->setDisplayOptions('form', [
    //     'type' => 'link_default',
    //     'weight' => -3,
    //   ])
    //   ->setDisplayConfigurable('form', TRUE)
    //   ->setDisplayOptions('view', [
    //     'type' => 'link',
    //     'weight' => -3,
    //   ])
    //   ->setDisplayConfigurable('view', TRUE);

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
    $html_id = $this->getPluginDefinition()['id'];

    return [
      'placeholder' => [
        '#type' => 'html_tag',
        '#tag' => 'ad-content',
        '#attributes' => [
          'id' => Html::getUniqueId($html_id),
        ],
        '#attached' => [
          'library' => ['advertisement/advertisement'],
        ],
      ],
    ];
  }

}
