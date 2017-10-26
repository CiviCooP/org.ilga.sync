<?php
/**
 * Sync.Send API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sync_Send_spec(&$spec) {

}

/**
 * Sync.Send API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sync_Send($message) {
  $params['payload'] = json_encode($message);
  $contactId = CRM_Sync_Utils_DB::findLocalId($message['ilga_identifier']);
  $returnValues = CRM_Sync_Utils_Rest::call('receive',$params);
  if($returnValues['is_error']){
    throw new API_Exception($returnValues['error_message']);
  } else {
    $obs =  new CRM_Sync_Observer();
    $obs->untag($contactId);
    return civicrm_api3_create_success($returnValues, $message, 'Sync', 'send');
  }
}
