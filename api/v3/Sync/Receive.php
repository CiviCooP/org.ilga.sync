<?php
/**
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 20-10-17 17:58
 *  @license AGPL-3.0
 *
 */

/**
 * Sync.Receive API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sync_Receive_spec(&$spec) {
  $spec['payload']['api.required'] = 1;
}

/**
 * Sync.Receive API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sync_Receive($params) {
  $payload = $params['payload'];
  $message = json_decode($payload,true);

  if($params['merge']){
    $config = CRM_Sync_Config::singleton();
    $region = $config->get('ilgasync_destination')=='region';
    if($region){
      $contactId = CRM_Sync_Utils_DB::findContactId($message['ilga_identifier']);
    } else {
      $contactId = $message['ilga_identifier'];
    }

    if($region){
      $contactId = CRM_Sync_Utils_DB::findContactId($message['ilga_identifier']);
      $local = CRM_Sync_Message::construct($contactId);
      // this is the region - so the incoming message comes
      // from the hq
      $merged = CRM_Sync_Message::merge($message,$local);
    } else {
      $contactId = $message['ilga_identifier'];
      $local = CRM_Sync_Message::construct($contactId);
      // these are the hq, so the incoming message is
      // region
      $merged = CRM_Sync_Message::merge($local,$message);
    }
    CRM_Sync_Message::process($merged);
  } else {
    // nothing difficult - just process the message
    CRM_Sync_Message::process($message);
  }
  return civicrm_api3_create_success(null, $params, 'Sync', 'receive');
}
