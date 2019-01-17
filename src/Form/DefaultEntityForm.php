<?php
namespace Drupal\publicity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Number as NumberUtility;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBase;
/**
 * Class DefaultEntityForm.
 */
class DefaultEntityForm extends EntityForm {
  /**
   * @var $entity_type Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type;
  /**
   * @var $connection Drupal\Core\Database\Connection
   */
  protected $connection;
  /**
   * @var $connection Drupal\Core\Database\Connection
   */
  protected $delta;

  /**
   * Class construct
   * 
   * @param $entity_type Drupal\Core\Entity\EntityTypeManager
   *  The entity type manager
   * 
   * @param $connection Drupal\Core\Database\Connection
   *  The connection to database
   */
  public function __construct(EntityTypeManager $entity_type, Connection $connection) {
    $this->entity_type = $entity_type;
    $this->connection = $connection;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('database')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $default_entity = $this->entity;
    $class = get_class($this);
    $form['#attributes']['novalidate'] = 'novalidate';
    // Disable caching for the form
    $form['#cache'] = ['max-age' => 0];
    
    // Do not flatten nested form fields
    $form['#tree'] = TRUE;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $default_entity->label(),
      '#placeholder'=>t('Configuration Entity Name'),
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
    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Url'),
      '#default_value' => $default_entity->url,
      '#placeholder'=>t('https://yourwebsite.com'),
      '#required' => TRUE,
    ];
    $form['id_publicity'] = [
      '#type' => 'textfield',
      '#title' => $this->t('ID'),
      '#maxlength' => 255,
      '#default_value' => $default_entity->id_publicity,
      '#placeholder'=>t('Example: WPX992'),
      '#required' => TRUE,
      '#element_validate'=>[
        [$class, 'validateIdpublicity'],
      ],
    ];
    $data_taxonomy = $this->taxonomy_vocabulary_get_names();
    $data_content_type = $this->content_type_get_names();
    $form['render_section'] = [
      '#type' => 'select',
      '#title' => $this->t('Render Section'),
      '#default_value' => $default_entity->getPlace(),
      '#description' => $this->t('The place where the ad will be displayed'),
      '#options' => [
        'Taxonomies' => $data_taxonomy,
        'Content Types' => $data_content_type,
      ],
      '#required' => TRUE,
    ];
    $form['measurement']=[
      '#type'=>'radios',
      '#title'=>$this->t('Measurement'),
      '#default_value'=> $default_entity->measurement,
      '#options'=>[
        'pixel'=>$this->t('Pixel'),
        'percentage'=>$this->t('Percentage'),
        ],
      '#required'=>TRUE,
      ];
    $form['breakpoints'] = [
      '#type' => 'fieldset',
      '#tree' => TRUE,
      '#description' => '',
      '#title' => $this->t('Breakpoints'),
      '#prefix' => '<div id="breakpoint-wrapper">',
      '#suffix' => '</div>',
    ];
    $width = $default_entity->getBreakpoints();
    if(!empty($width)) {
      $form_state->set('field_deltas', range(0,count($width['form']) - 1));
    }
    if ($form_state->get('field_deltas') == '') {
      $form_state->set('field_deltas', range(0,0));
    }
    
    $field_count = $form_state->get('field_deltas');
  
    foreach ($field_count as $delta) {
      $this->delta = $delta;
      $form['breakpoints']['form'][$delta] = [
        '#type' => 'fieldset',
        '#title' => $this->t('RESPONSIVE DESIGN'),
        '#tree' => TRUE,
      ];
      $form['breakpoints']['form'][$delta]['pixelwidth'] = [
        '#type' => 'number',
        '#title' => 'Width', 
        '#min' => 1,
        '#default_value' => $width['form'][$delta]['pixelwidth'],
        '#description' => $this->t('Example: 350 px'),
        '#min'=>1,
        '#max'=> 1999,
        '#step' => 1,
        '#states' => [
          'visible' => [
          ':input[name="measurement"]' => ['value' => 'pixel'],
          ],
          'required' => [
          ':input[name="measurement"]' => ['value' => 'pixel'],
          ],
        ],
        '#element_validate'=>[
          [$class, 'validateNumber'],
        ],
      ];
      ;
      $form['breakpoints']['form'][$delta]['pixelheight'] = [
        '#type' => 'number',
        '#title' => 'Height', 
        '#min' => 1,
        '#default_value' => $width['form'][$delta]['pixelheight'],
        '#description' => $this->t('Example: 750px'),
        '#step' => 1,
        '#min'=>1,
        '#max'=> 1999,
        '#states' => [
        'visible' => [
          ':input[name="measurement"]' => ['value' => 'pixel'],
          ],
          'required' => [
          ':input[name="measurement"]' => ['value' => 'pixel'],
          ],
        ],
        '#element_validate'=>[
          [$class, 'validateNumber'],
        ],
      ];
      
        $form['breakpoints']['form'][$delta]['percentagewidth'] = [
        '#type' => 'number',
        '#title' => 'Width', 
        '#min' => 1,
        '#default_value' => $width['form'][$delta]['percentagewidth'],
        '#description' => $this->t('Example: 35%'),
        '#min'=>1,
        '#max'=> 99,
        '#step' => 1,
        '#states' => [
          'visible' => [
          ':input[name="measurement"]' => ['value' => 'percentage'],
          ],
          'required' => [
          ':input[name="measurement"]' => ['value' => 'percentage'],
          ],
        ],
        '#element_validate'=>[
          [$class, 'validateNumber'],
        ],
      ];
      
      $form['breakpoints']['form'][$delta]['percentageheight'] = [
        '#type' => 'number',
        '#title' => 'Height', 
        '#min' => 1,
        '#default_value' => $width['form'][$delta]['percentageheight'],
        '#description' => $this->t('Example: 75%'),
        '#step' => 1,
        '#min'=>1,
        '#max'=> 99,
        '#states' => [
        'visible' => [
          ':input[name="measurement"]' => ['value' => 'percentage'],
          ],
          'required' => [
          ':input[name="measurement"]' => ['value' => 'percentage'],
          ],
        ],
        '#element_validate'=>[
        [$class, 'validateNumber'],
        ],
      ];
      $form['breakpoints']['form'][$delta]['device']=[
        '#type'=>'select',
        '#options'=>[
          'Desktop','Mobile','Tablet',
        ],
        '#empty_option'=>'Devices',
        '#description'=> $this->t('Choose a device for your configuration'),
        '#states'=>[
          'visible'=>[
            ':input[name="measurement"]'=>['checked'=>TRUE],
          ],
          'required' => [
          ':input[name="measurement"]' => ['value' => 'pixel'],
          ],
        ],
        '#default_value'=> $width['form'][$delta]['device'],
      ];
      $form['breakpoints']['form'][$delta]['remove'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove'),
        '#submit' => ['::addMoreRemove'],
        '#ajax' => [
          'callback' => '::addMoreRemoveCallback',
          'wrapper' => 'breakpoint-wrapper',
        ],
        '#name' => 'remove_name_' . $delta,
        '#states'=>[
          'visible'=>[
            ':input[name="measurement"]'=>['checked'=>TRUE]
          ]
        ],
      ];
    }
    $form['breakpoints']['add'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add'),
      '#submit' => ['::AddMoreAddOne'],
      '#ajax' => [
        'callback' => '::AddMoreAddOneCallback',
        'wrapper' => 'breakpoint-wrapper',
      ],
    ];
    return $form;
  }
  /**
   * function to add one field of breakpoint.
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
	public function addMoreRemove(array &$form, FormStateInterface $form_state){
		// Get the triggering item
    $delta_remove = $form_state->getTriggeringElement()['#parents'][2];
    
    // Store our form state
    $field_deltas_array = $form_state->get('field_deltas');
    
    // Find the key of the item we need to remove
    $key_to_remove = array_search($delta_remove, $field_deltas_array);
    
    // Remove our triggered element
    unset($field_deltas_array[$key_to_remove]);
    
    // Rebuild the field deltas values
    $form_state->set('field_deltas', $field_deltas_array);
    
    // Rebuild the form
    $form_state->setRebuild();
    return $this->messenger()->addMessage($this->t('The BreakPoint has been remove'), 'warning');
	}
  /**
   * ajax callback to add the new field to the render form.
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
  public function addMoreRemoveCallback(array &$form, FormStateInterface $form_state) {
		return $form['breakpoints'];
	}
  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
	public function AddMoreAddOne(array &$form, FormStateInterface $form_state) {
    // Store our form state
    $field_deltas_array = $form_state->get('field_deltas');
    
    // check to see if there is more than one item in our array
    if (count($field_deltas_array) > 0) {
      // Add a new element to our array and set it to our highest value plus one
      $field_deltas_array[] = max($field_deltas_array) + 1;
    }
    else {
      // Set the new array element to 0
      $field_deltas_array[] = 0;
    }
  
    // Rebuild the field deltas values
    $form_state->set('field_deltas', $field_deltas_array);
  
    // Rebuild the form
    $form_state->setRebuild();
    
  }
  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return mixed
   */
  function AddMoreAddOneCallback(array &$form, FormStateInterface $form_state) {
    return $form['breakpoints'];
  }
  //Functions to validate fields of form
 
  public static function validateNumber(&$element, FormStateInterface $form_state, &$complete_form) {
    // var_dump($element); die();
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
      if (isset($element['#required']) &&  $value <=> $element['#required']) {
      $form_state->setError($element, t('%name is required'));
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
    if (!preg_match('/^[a-z ]{3,25}$/', $value)) {
      $form_state->setError($element, t('Please. Write only data type string. Minimum 5 characters and Maximum 25'));
    }
  }
  public static function validateIdpublicity(&$element, FormStateInterface $form_state, &$complete_form){
    $value = $element['#value'];
    $value = strtolower($value);
    if (!preg_match('/^[a-z0-9]{6}$/', $value)){
      $form_state->setError($element, t('Please. Write only data type string. Three Numbers and three characters (WXY457)'));
    }
  }  
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // var_dump($form_state->getValue('measurement'));die();
    
    $default_entity = $this->entity;
    $value_breakpoints= $form_state->getValue('breakpoints', 'form');
    $default_entity->setBreakpoints($value_breakpoints);
    $place = $form_state->getValue('render_section');
    $this->checkStatusEntity($place);
    $status = $default_entity->save();
    
    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Advertising entity.', [
          '%label' => $default_entity->label(),
        ]));
        break;
      default:
        $this->messenger()->addMessage($this->t('Saved the %label Custom Publicity Entity.', [
          '%label' => $default_entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($default_entity->toUrl('collection'));
  }

  /**
   * Get names for all taxonomy vocabularies.
   * 
   * @return array array A list of existing vocabulary IDs.
   */
  public function taxonomy_vocabulary_get_names() {
    $names =& drupal_static(__FUNCTION__);
    if (!isset($names)) {
      $names = [];
      $config_names = \Drupal::configFactory()
        ->listAll('taxonomy.vocabulary.');
      foreach ($config_names as $config_name) {
        $id = substr($config_name, strlen('taxonomy.vocabulary.'));
        $names[$id] = entity_load('taxonomy_vocabulary', $id)->label();
      }
    }
    return $names;
  }
  /**
   * Get names for all content types.
   * 
   * @return array array A list of existing content types IDs.
   */
  public function content_type_get_names() {
    $names =& drupal_static(__FUNCTION__);
    if (!isset($names)) {
      $names = [];
      $config_names = \Drupal::configFactory()
        ->listAll('node_type.');
      foreach ($config_names as $config_name) {
        $id = substr($config_name, strlen('node_type.'));
        $names[$id] = entity_load('node_type', $id)->label();
        
      }
    }
    return $names;
  }
  /**
   * Check if a node is published or not to render the AD.
   * 
   * @param $machine_name
   *  the name of the content type to load his nodes.
   * 
   * @return object \Drupal::messenger
   */
  private function checkStatusEntity($machine_name) {
    
    if(array_key_exists($machine_name, $this->content_type_get_names())) {
      
      $nids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $machine_name)
        ->execute();
      $nodes = $this->entity_type
        ->getStorage('node')
        ->loadMultiple($nids);
      
      foreach ($nodes as $node) {
        $data_nodes[] = $node->label();
        $message = $this->t('In the following nodes the publication will be rendered: ' . implode(',', $data_nodes));
      return $this->messenger()->addMessage($message);
      }
      
    }
  }
}