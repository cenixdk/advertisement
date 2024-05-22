<?php

declare(strict_types=1);

namespace Drupal\advertisement\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for the advertisement module.
 */
final class AdvertisementSettingsForm extends ConfigFormBase {
  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'advertisement_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advertisement.settings'];
  }

  /**
   * AdSettingsForm constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    DateFormatterInterface $date_formatter,
  ) {
    parent::__construct($config_factory);

    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('advertisement.settings');

    $form['settings'] = [
      '#markup' => $this->t('Configurations for the Advertisement module'),
    ];

    $form['caching'] = [
      '#type' => 'details',
      '#title' => $this->t('Caching'),
      '#open' => TRUE,
    ];

    $period = [0, 60, 180, 300, 600, 900, 1800, 2700, 3600, 10800, 21600, 32400, 43200, 86400];
    $period = array_map([$this->dateFormatter, 'formatInterval'], array_combine($period, $period));
    $period[0] = '<' . $this->t('no caching') . '>';
    $form['caching']['page_cache_maximum_age'] = [
      '#type' => 'select',
      '#title' => $this->t('Override browser and proxy cache maximum age for advertisements'),
      '#options' => $period,
      '#default_value' => $config->get('cache.page.max_age') ?? 0,
      '#description' => $this->t('This is used as the value for max-age in Cache-Control headers instead of the site\'s default cache setting.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('advertisement.settings')
      ->set('cache.page.max_age', $form_state->getValue('page_cache_maximum_age'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
