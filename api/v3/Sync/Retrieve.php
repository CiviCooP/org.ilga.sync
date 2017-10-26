<?php

/**
 * Sync.Retrieve API specification (optional)
 * Receives a synchronization message from the other system
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sync_Retrieve_spec(&$spec) {
  $spec['destination']['api.required'] = 1;
  $spec['ilga_identifier']['api.required'] = 1;
}

/**
 * Sync.Retrieve API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sync_Retrieve($params) {

  $config = CRM_Sync_Config::singleton();
  $destination = $params['destination'];
  $ilgaId = $params['ilga_identifier'];
  $returnValues = array();

  if(empty($destination)){
    throw new API_Exception('Sync retrieve needs a destination');
  }

  if($config->get('ilgasync_destination')!=$destination){
    throw new API_Exception("Message retrieved from wrong destination message asks {$destination} service provides {$config->get('ilgasync_destination')}   ");
  }

  if($destination=='hq'){
    $contactId=$ilgaId;
    $returnValues['found'] = 1;
  } else {
    $contactId = CRM_Sync_Utils_DB::findContactId($ilgaId);
    if(empty($contactId)){
      $returnValues['found'] = 0;
      return civicrm_api3_create_success($returnValues, $params, 'Sync', 'Retrieve');
    } else {
      $returnValues['found'] = 1;
    }
  }
  $message = CRM_Sync_Message::construct($contactId,$destination);
  $message['ilga_identifier'] = $ilgaId;
  $returnValues['payload']=json_encode($message);
  return civicrm_api3_create_success($returnValues, $params, 'Sync', 'Retrieve');
}
