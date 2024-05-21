<?php

declare(strict_types=1);

namespace Drupal\advertisement\Entity;

use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the advertisement entity class.
 *
 * @ContentEntityType(
 *   id = "advertisement",
 *   label = @Translation("Advertisement"),
 *   label_collection = @Translation("Advertisements"),
 *   label_singular = @Translation("Advertisement"),
 *   label_plural = @Translation("Advertisements"),
 *   label_count = @PluralTranslation(
 *     singular = "@count advertisement",
 *     plural = "@count advertisements",
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "list_builder" = "Drupal\advertisement\Entity\AdvertisementListBuilder",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\advertisement\Form\AdvertisementForm",
 *       "edit" = "Drupal\advertisement\Form\AdvertisementForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "default" = "Drupal\Core\Entity\ContentEntityForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "advertisement",
 *   revision_table = "advertisement_revision",
 *   admin_permission = "administer advertisement",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "revision_id",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *     "owner" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_user",
 *     "revision_created" = "revision_created",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "collection" = "/admin/content/advertisement",
 *     "add-form" = "/advertisement/add",
 *     "canonical" = "/advertisement/{advertisement}",
 *     "edit-form" = "/advertisement/{advertisement}/edit",
 *     "delete-form" = "/advertisement/{advertisement}/delete",
 *   },
 *   field_ui_base_route = "entity.advertisement.settings",
 *   render_cache = TRUE,
 * )
 */
class Advertisement extends EditorialContentEntityBase implements AdvertisementInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

  /**
   * Get advertisement Identifier.
   */
  public function getAdvertisementIdentifier(): string {
    return $this->uuid();
  }

  /**
   * Get advertisement target URL.
   */
  public function getUrl(): ?Url {
    $value = current($this->get('url')->getValue());
    return $value ? Url::fromUri($value['uri'], $value['options']) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage): void {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    /** @var \Drupal\Core\Field\BaseFieldDefinition[] $fields */
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['status']
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Title'))
      ->setRequired(TRUE)
      ->addConstraint('UniqueField', [])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Authored by'))
      ->setDescription(t('The user ID of the author.'))
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['image'] = BaseFieldDefinition::create('image')
      ->setLabel(new TranslatableMarkup('Image'))
      ->setDisplayOptions('form', [
        'type' => 'managed_file',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setSettings([
        'file_directory' => 'advertisement_images',
        'alt_field' => TRUE,
        'file_extensions' => 'png jpg jpeg',
      ])
      ->setRequired(TRUE);

    $fields['url'] = BaseFieldDefinition::create('link')
      ->setLabel(new TranslatableMarkup('URL'))
      ->setDescription(new TranslatableMarkup('The URL to be taken to when clicking on the AD.'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'link_default',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'link',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(new TranslatableMarkup('Authored on'))
      ->setDescription(new TranslatableMarkup('The time that the AD was created.'))
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'timestamp',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(new TranslatableMarkup('Changed'))
      ->setDescription(new TranslatableMarkup('The time that the AD was last edited.'));
    return $fields;
  }

}
