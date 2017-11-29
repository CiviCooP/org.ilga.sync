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
class CRM_Sync_Form_Preferences  extends CRM_Admin_Form_Preferences {

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

  /* here alle the settings are defined - every setting starts with 'ilga' */
  public function preProcess() {
    CRM_Utils_System::setTitle(ts('Ilga Synchronization Component Settings'));
    $this->_varNames = array(
     'Ilga Synchronization' => array(
        'ilgasync_destination' => array(
          'html_type' => 'radio',
          'title' => ts('Is this instance the HQ'),
          'weight' => 1,
          'description' => ts('The HQ instance contains all the contacts, the region just a subset'),
        ),
        'ilgasync_region' =>  array (
          'html_type' => 'select',
          'title' => ts('Region to sync to'),
          'option_values' => array('' => ts('- select -')) + CRM_Core_PseudoConstant::worldRegion(),
          'weight' => 2,
          'description' => ts('Description of the region'),
        ),
       'ilgasync_url' =>  array (
         'html_type' => 'text',
         'title' => ts('Remote REST Api URL'),
         'size'  => 64,
         'weight' => 3,
         'description' => ts('Example https://example.com/sites/all/modules/civicrm/extern/rest.php'),
       ),
       'ilgasync_apikey' =>  array (
         'html_type' => 'text',
         'title' => ts('Api Key'),
         'weight' => 4,
         'description' => ts('Api key of the user used of the sync'),
       ),
       'ilgasync_sitekey' =>  array (
         'html_type' => 'text',
         'title' => ts('Site Key'),
         'weight' => 5,
         'description' => ts('Site key (can be found in the civicrm.settings.php file'),
       ),
       'ilgasync_dryrun' =>  array (
         'html_type' => 'checkbox',
         'title' => ts('Dry Run'),
         'weight' => 6,
         'description' => ts('Do not send the data, show only what is send (not active)'),
       ),
       'ilgasync_default_address' =>  array (
         'html_type' => 'select',
         'title' => ts('Default Address'),
         'option_values' => $this->locationTypes(),
         'weight' => 7,
         'description' => ts('Address type used when a new address is created'),
       ),
       'ilgasync_default_phone' =>  array (
         'html_type' => 'select',
         'title' => ts('Default Phone'),
         'option_values' => $this->locationTypes(),
         'weight' => 9,
         'description' => ts('Phone type used when a new phone is inserted'),
       ),
       'ilgasync_default_email' =>  array (
         'html_type' => 'select',
         'title' => ts('Default Email'),
         'option_values' => $this->locationTypes(),
         'weight' => 10,
         'description' => ts('Email type used when a new email is inserted'),
       ),
       'ilgasync_default_website' =>  array (
         'html_type' => 'select',
         'title' => ts('Default Website'),
         'option_values' => $this->optionValues('website_type'),
         'weight' => 11,
         'description' => ts('Url type for the main website'),
       ),
      ),
    );
    parent::preProcess();
  }

  public function buildQuickForm() {
    parent::buildQuickForm();

    /* give the ilgasync url more space to enter data */
    $idx = $this->_elementIndex['ilgasync_url'];
    $this->_elements[$idx]->setSize(50);
    $this->_elements[$idx]->setMaxLength(200);

  }
}
