<?php

declare(strict_types=1);

namespace Drupal\advertisement;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an advertisement entity type.
 */
interface AdvertisementInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
