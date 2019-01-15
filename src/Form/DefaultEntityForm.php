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
      '#id'=>'measurement',
      '#type'=>'radios',
      '#options'=> [
        'pixels'=>t('Pixels'),
        'percentage'=>t('Percentage'),
      ],
      '#ajax'=>[
        'callback'=> '::disabledFieldsCallback',
        'wrapper'=>'breakpoints-wrapper',
        'event'=>'change',
        'progress'=>[
          'type'=>'throbber',
          'message'=>'Loading',
        ],
      ],
      '#default_value'=> $default_entity->get('measurement'), 
    ];
    $form['breakpoints']['pixel_width'] = [
      '#prefix' => '<div id="breakpoint-wrapper">',
      '#id'=>'pixel-width', 
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description'=> 'Example: 780px',
      '#default_value'=> $default_entity->get('pixel_width'),
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#states'=>[
        'visible'=>[
        ':input[name="measurement"]' =>['value'=>'pixels'],
        ],
      ],
      '#min'=>100,
      '#max'=> 1999,
      '#step' => 1,
      '#required' => TRUE,
    ];
    $form['breakpoints']['percentage_width'] = [
      '#id'=>'percentage-width',
      '#type' => 'number',
      '#title' => $this->t('Width'),
      '#description'=> 'Example: 45%',
      '#default_value'=> $default_entity->get('percentage_width'),
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#states'=>[
        'visible'=>[
          ':input[name="measurement"]'=>['value'=>'percentage'],
        ],
      ],
      '#min'=>15,
      '#max'=> 100,
      '#step' => 1,
      '#required' => TRUE,
    ];
    $form['breakpoints']['pixel_height']= [
      
      '#id'=>'pixel-height',
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#description'=> 'Example: 1080px',
      '#default_value'=> $default_entity->get('pixel_height'),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#states'=>[
        'visible'=>[
          ':input[name="measurement"]'=>['value'=>'pixels'],
        ],
      ],
      '#step' => 1,
      '#min'=>100,
      '#max'=> 1999,
    ];
     $form['breakpoints']['percentage_eight']= [
      '#id'=>'percentage-height',
      '#type' => 'number',
      '#title' => $this->t('Height'),
      '#description'=> 'Example: 70%',
      '#default_value'=> $default_entity->get('percentage_eight'),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateNumber'],
      ],
      '#states'=>[
        'visible'=>[
          ':input[name="measurement"]'=>['value'=>'percentage'],
        ],
      ],
      '#step' => 1,
      '#min'=>15,
      '#max'=> 100,
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
  public function disable_html5_validation_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
  $form['#attributes']['novalidate'] = 'novalidate';
  }
  public static function disabledFieldsCallback(array $form, FormStateInterface $form_state,$form_id){
    if(($form_state->getValue('pixel_heights'))>0){
     $form['breakpoints']['percentage_eight']['#required']= FALSE;
      $form['breakpoints']['percentage_width']['#required']= FALSE;
    }elseif(($form_state->getValue('percentage_height'))>0){
      $form['breakpoints']['pixel_height']['#required']= FALSE;
      $form['breakpoints']['pixel_width']['#required']= FALSE;
    }else{
        echo 'An unexpected error has ocurred';
    }
    return $form;
  }

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

    if (!preg_match('/^[a-z ]{3,15}$/', $value)) {
      $form_state->setError($element, t('Please. Write only data type string. Minimum 3 characters and Maximum 15'));
    }
  }
  public static function validateIdpublicity(&$element, FormStateInterface $form_state, &$complete_form){
    $value = $element['#value'];
    $value = strtolower($value);

    if (!preg_match('/^[a-z0-9]{6}$/', $value)){
      $form_state->setError($element, t('Please. Write only data type string. Minimum 3 characters and Maximum 15'));
    }
  }  
   /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /* var_dump($form_state->getValue('measurement')); die(); */
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
