<?php

namespace Drupal\publicity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Number as NumberUtility;
use Drupal\Core\Render\Element;

/**
 * Class DefaultEntityForm.
 */
class DefaultEntityForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $default_entity = $this->entity;
    $class = get_class($this);
    /*var_dump($fields); die();*/
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $default_entity->label(),
      '#description' => $this->t("Label for the Default entity."),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateString'],
      ],
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $default_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\publicity\Entity\DefaultEntity::load',
      ],
      '#disabled' => !$default_entity->isNew(),
    ];
    $form['url']=[
      '#type'=>'url',
      '#title'=>t('Url'),
      '#description'=>'Add a custom url for your configuration',
      '#default_value'=>$default_entity->get('url'),
      '#required' => TRUE,
    ];
    $form['id_publicity']=[
      '#type'=>'textfield',
      '#title'=>$this->t('ID Publicity'),
      '#default_value'=> $default_entity->get('id_publicity'),
      '#placeholder'=>$this->t('Ej: XUY146'),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateIdpublicity'],
      ],
    ];
    $form['render_section']=[
      '#type'=> 'select',
      '#default_value'=> $default_entity->get('render_section'),
      '#options'=>[
        'Home Page','Article','Sections',
      ],
      '#empty_option'=>'Render Sections',
      '#required' => TRUE,
    ];
    $form['breakpoints'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('BREAKPOINTS'),
    ];
    $form['breakpoints']['measurement']=[
      '#type'=>'radios',
      '#options'=> [
        'Pixels','Percentage',
      ],
      '#ajax' => [
        'callback' => '::configurationOptionsCallback',
        'wrapper' => 'breakpoint-wrapper',
        'event' => 'change',
      ],
    ];
    $form['breakpoints']['width'] = [
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description'=>$this->t('Ej: 720px'),
      '#default_value'=> $default_entity->get('width'),
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#min'=>10,
      '#max'=> 1999,
      '#step' => 1,
      '#required' => TRUE,
      '#prefix' => '<div id="breakpoint-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['breakpoints']['height']= [
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#description'=> $this->t('Ej: 1080px'),
      '#default_value'=> $default_entity->get('height'),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#step' => 1,
      '#min'=>10,
      '#max'=> 1999,
      '#prefix' => '<div id="breakpoint-wrapper">',
      '#suffix' => '</div>',
    ];
    $form['breakpoints']['device']=[
      '#type'=>'select',
      '#options'=>[
        'Desktop','Mobile','Tablet',
    ],
      '#title'=>t('Device'),
      '#default_value'=> $default_entity->get('device'),
      '#placeholder'=>$this->t('Custom Devices'),
      '#empty_option'=>$this->t('Devices List'),
      '#required' => TRUE,
    ];
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public static function validateNumber(&$element, FormStateInterface $form_state, &$complete_form) {
    $value = $element['#value'];
    if ($value === '') {
      return;
    }

    $name = empty($element['#title']) ? $element['#parents'][0] : $element['#title'];

    // Ensure the input is numeric.
    if (!is_numeric($value)) {
      $form_state->setError($element, t('%name must be a number.', ['%name' => $name]));
      return;
    }

    // Ensure that the input is greater than the #min property, if set.
    if (isset($element['#min']) && $value < $element['#min']) {
      $form_state->setError($element, t('%name must be higher than or equal to %min.', ['%name' => $name, '%min' => $element['#min']]));
    }

    // Ensure that the input is less than the #max property, if set.
    if (isset($element['#max']) && $value > $element['#max']) {
      $form_state->setError($element, t('%name must be lower than or equal to %max.', ['%name' => $name, '%max' => $element['#max']]));
    }

    if (isset($element['#step']) && strtolower($element['#step']) != 'any') {
      // Check that the input is an allowed multiple of #step (offset by #min if
      // #min is set).
      $offset = isset($element['#min']) ? $element['#min'] : 0.0;

      if (!NumberUtility::validStep($value, $element['#step'], $offset)) {
        $form_state->setError($element, t('%name is not a valid number.', ['%name' => $name]));
      }
    }
  }
  /**
   * {@inheritdoc}
   */
  public static function validateString(&$element, FormStateInterface $form_state, &$complete_form) {
   $value = $element['#value'];
    $value = strtolower($value);

    if (!preg_match('/^[a-z]{3,15}$/', $value)) {
      $form_state->setError($element, t('Please. Write only data type string. Minimum 3 characters and Maximum 15'));
    }
  }
  public static function validateIdpublicity(){
    $value = $element['#value'];
    $value = strtolower($value);

    if (!preg_match('/^[a-z0-9]{6}$/', $value)) {
      $form_state->setError($element, t('Please. Write only data type string. Minimum 3 characters and Maximum 15'));
    }
  }
  /**
   * {@inheritdoc}
   */
  public function configurationOptionsCallback(&$element, FormStateInterface $form_state){
    switch ($form_state) {
      case 'Pixels':
        $description_width = 'Ej: 720px';
        $description_height = 'Ej: 1080px';
        $ajax_response->addCommand(new HtmlCommand('#edit-width--description', $description_width));
        $ajax_response->addCommand(new HtmlCommand('#edit--height--description', $description_height));
        break;
      case 'Percentage':
        $description_width = 'Ej: 70%';
        $description_height = 'Ej: 70%';
        $ajax_response->addCommand(new HtmlCommand('#edit-width--description', $description));
        $ajax_response->addCommand(new HtmlCommand('#edit--height--description', $description));
        break;
      
      default:
        echo 'An unexpected error has occurred';
        break;
    }
  }
   /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $default_entity = $this->entity;
    $status = $default_entity->save();
    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Default entity.', [
          '%label' => $default_entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Default entity.', [
          '%label' => $default_entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($default_entity->toUrl('collection'));
  }
}
