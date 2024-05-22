<?php

namespace Drupal\advertisement\Controller;

use Drupal\advertisement\Entity\AdvertisementInterface;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Acvertisement controller.
 */
class AdvertisementController extends ControllerBase implements ContainerInjectionInterface {
  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
    );
  }

  /**
   * Renders the Advertisements.
   */
  public function render(Request $request): CacheableJsonResponse {
    $advertisementConfig = \Drupal::config('advertisement.settings');
    $cacheConfig = \Drupal::config('system.performance');

    $request_data = $request->query->all();
    $build = $this->buildAdvertisement();
    $response_data[$request_data['id']] = $this->renderer->renderPlain($build);

    // Get cache setting from module if present else use system setting.
    $cacheMaxAge = $advertisementConfig->get('cache.page.max_age') ?? $cacheConfig->get('cache.page.max_age');

    $response_data['#cache'] = [
      'max-age' => intval($cacheMaxAge),
      'contexts' => [
        'url',
      ],
    ];

    $response = new CacheableJsonResponse($response_data);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($response_data));

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function buildAdvertisement(): array {
    $build = [];

    $advertisement = $this->getRandomAdvertisement();
    if ($advertisement) {
      $build = \Drupal::entityTypeManager()
        ->getViewBuilder($advertisement->getEntityTypeId())
        ->view($advertisement);
    }

    return $build;
  }

  /**
   * Retrieves a random Advertisement.
   *
   * @return \Drupal\advertisement\Entity\AdvertisementInterface|null
   *   An Advertisement entity or NULL if none could be found.
   */
  private function getRandomAdvertisement(): ?AdvertisementInterface {
    $advertisement = NULL;

    try {
      /** @var \Drupal\Core\Entity\ContentEntityStorageInterface $storage */
      $storage = \Drupal::entityTypeManager()->getStorage('advertisement');

      $result = $storage->getQuery()
        ->accessCheck()
        ->condition('status', 1)
        ->execute();

      if ($result) {
        /** @var \Drupal\advertisement\Entity\AdvertisementInterface $advertisement */
        $advertisement = $storage->load($result[array_rand($result)]);
      }
    }
    catch (PluginException $e) {
      watchdog_exception('advertisement', $e);
    }

    return $advertisement;
  }

}
