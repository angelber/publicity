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
 *     "url" = "url"
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
   * The Advertising entity ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Advertising entity label.
   *
   * @var string
   */
  public $label;

  /**
   * The Advertising entity Url.
   *
   * @var string
   */
  public $Url;

  /**
   * The Advertising entity id of ad.
   *
   * @var string
   */
  public $ad_id;

  /**
   * The Advertising entity place.
   *
   * @var string
   */
  protected $place;

  /**
   * The Advertising entity breakpoints.
   *
   * @var array
   */
  public $breakpoints;

   /**
   * Set the default place to put an AD.
   *
   * @param string $place
   *   The place to set.
   *
   * @return string
   */

  public function getUrl() {
    return $this->get('Url');
  }

  public function setUrl(string $Url) {
    return $this->set('Url', $Url);
  }


  public function setPlace($place) {
    return $this->set('place', $place);
  }

  /**
   * Get the default place to put an AD.
   *
   * @return string
   */
  public function getPlace() {
    return $this->get('place');
  }

  /**
   * Set the default breakpoints.
   *
   * @param string $breakpoints
   *   The breakpoints to set.
   *
   * @return string
   */
  public function setBreakpoints($breakpoints) {
    $serializer = \Drupal::service('serialization.phpserialize');
    $this->breakpoints = $serializer->encode($breakpoints);
  }

  /**
   * Get the breakpoints.
   *
   * @return string
   */
  public function getBreakpoints() {
    $serializer = \Drupal::service('serialization.phpserialize');
    return $serializer->decode($this->breakpoints);
  }
}
