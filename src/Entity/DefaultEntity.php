<?php

namespace Drupal\publicity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Default entity entity.
 *
 * @ConfigEntityType(
 *   id = "default_entity",
 *   label = @Translation("Default entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\publicity\DefaultEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\publicity\Form\DefaultEntityForm",
 *       "edit" = "Drupal\publicity\Form\DefaultEntityForm",
 *       "delete" = "Drupal\publicity\Form\DefaultEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\publicity\DefaultEntityHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "default_entity",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "url" = "url",
 *     "render_section" = "render_section",
 *     "width" = "pixel_width",
 *     "height" = "pixel_height",
 *      "percentage_width" = "percentage_width",
 *     "percentage_eight" = "percentage_eight",
 *     "device"="device",
 *     "measurement"="measurement"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/default_entity/{default_entity}",
 *     "add-form" = "/admin/structure/default_entity/add",
 *     "edit-form" = "/admin/structure/default_entity/{default_entity}/edit",
 *     "delete-form" = "/admin/structure/default_entity/{default_entity}/delete",
 *     "collection" = "/admin/structure/default_entity"
 *   }
 * )
 */
class DefaultEntity extends ConfigEntityBase implements DefaultEntityInterface {

  /**
   * The Default entity ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Default entity label.
   *
   * @var string
   */
  protected $label;


  /**
   * The Default entity label.
   *
   * @var url
   */
  protected $url;


  /**
   * The Default entity label.
   *
   * @var string
   */
  protected $render_section;

  /**
   * The Default entity label.
   *
   * @var string
   */
  protected $pixel_width;

  /**
   * The Default entity label.
   *
   * @var integer
   */
  protected $pixel_height;

  /**
   * The Default entity label.
   *
   * @var integer
   */
  protected $percentage_width;

  /**
   * The Default entity label.
   *
   * @var integer
   */
  protected $percentage_eight;


  /**
   * The Default entity label.
   *
   * @var integer
   */
  protected $device;
  
  /**
   * The Default entity label.
   *
   * @var string
   */
  protected $measurement;
}

