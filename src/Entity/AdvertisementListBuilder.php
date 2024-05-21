<?php

declare(strict_types=1);

namespace Drupal\advertisement\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the advertisement entity type.
 */
class AdvertisementListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header = [
      'id' => $this->t('ID'),
      'title' => $this->t('Title'),
      'status' => $this->t('Status'),
      'uid' => $this->t('Author'),
      'created' => $this->t('Created'),
    ];

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var Drupal\advertisement\Entity\AdvertisementInterface $entity */

    $row = [
      'id' => $entity->id(),
      'title' => $entity->toLink(),
      'status' => $entity->isPublished() ? $this->t('published') : $this->t('not published'),
      'author' => [
        'data' => [
          '#theme' => 'username',
          '#account' => $entity->getOwner(),
        ],
      ],
      'created' => [
        'data' => $entity->get('changed')->view(['label' => 'hidden']),
      ],
    ];

    return $row + parent::buildRow($entity);
  }

}
