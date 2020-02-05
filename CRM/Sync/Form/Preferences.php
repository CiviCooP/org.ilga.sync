<?php
/**
 *  Form controller class for the preference form. By subclassing
 *  CRM_Admin_Form_Preferences the settings loading and saving
 *  are done automatically.
 *
 *  @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 24-10-17 9:54
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Form_Preferences  extends CRM_Core_Form {

  private $keys = ['ilgasync_destination','ilgasync_url','ilgasync_region','ilgasync_apikey',
     'ilgasync_sitekey','ilgasync_dryrun','ilgasync_default_address','ilgasync_default_phone','ilgasync_default_email','ilgasync_default_website'];

  /* helper functions for the selection list of the locations */
  private function locationTypes(){
    $locationTypes = array();
    $result = civicrm_api3('LocationType', 'get');
    foreach($result['values'] as $key=>$value){
      $locationTypes[$key]=$value['display_name'].' - '.$value['description'];
    }
    return $locationTypes;
  }

  /* helper function for the selection lists */
  private function optionValues($groupName){
    $optionValues = array();
    $result = civicrm_api3('OptionValue', 'get', array(
      'option_group_id' => $groupName,
    ));
    foreach($result['values'] as $key=>$value){
      $optionValues[$value['value']] = $value['label'];
    }
    return $optionValues;
  }

  /**
   * @return array|mixed|NULL
   */
  function setDefaultValues() {
    parent::setDefaultValues();
    foreach($this->keys as $key) {
      $values[$key] = Civi::settings()->get($key);
    }
    return $values;
  }

  public function buildQuickForm() {
    parent::buildQuickForm();
    $this->addRadio('ilgasync_destination',ts('Which instance is this?'),[
       'hq' => 'World',
       'region' => 'Region - Europe'
    ]);
    $this->add('select','ilgasync_region','Region to sync to',CRM_Core_PseudoConstant::worldRegion());
    $this->add('text','ilgasync_url','Remote REST Api URL',[
      'placeholder' =>  ts('https://example.com/sites/all/modules/civicrm/extern/rest.php'),
      'size' => 60
    ]);
    $this->add('text','ilgasync_apikey',ts('Api Key'),[
      'size' => 58
    ]);
    $this->add('text','ilgasync_sitekey',ts('Site Key'),[
      'size' => 58
    ]);
    $this->addRadio('ilgasync_dryrun',ts('Dry Run'),[
      'Y'=> 'Only Testing',
      'R'=>'Real Changes'
    ]);
    $this->add('select','ilgasync_default_address',ts('Default Address'),$this->locationTypes());
    $this->add('select','ilgasync_default_phone',ts('Default Phone'),$this->locationTypes());
    $this->add('select','ilgasync_default_email',ts('Default Email'),$this->locationTypes());
    $this->add('select','ilgasync_default_website',ts('Default Website'),$this->optionValues('website_type'));

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);
    $this->assign('elementNames', $this->getRenderableElementNames());
  }

  /**
   *
   */
  function postProcess() {
    $values = $this->exportValues();
    foreach($this->keys as $key)
    {
      Civi::settings()->set($key,$values[$key]);
    }
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
