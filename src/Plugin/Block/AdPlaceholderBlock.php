<?php

namespace Drupal\advertisement\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a banner ad placeholder block.
 *
 * @Block(
 *   id = "advertisement",
 *   admin_label = @Translation("Ad Placeholder"),
 *   category = @Translation("Advertisement")
 * )
 */
class AdPlaceholderBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new BookNavigationBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $advertisementConfig = \Drupal::config('advertisement.settings');
    $contentTypes = $advertisementConfig->get('targeting.content_types') ?? [];
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof Node && in_array($node->bundle(), array_values($contentTypes))) {
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
    }

    // Add cache metadata.
    $build['#cache'] = [
      'max-age' => $this->getCacheMaxAge(),
      'contexts' => ['url'],
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
