<?php

/**
 * Job.Synch API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_job_Sync_spec(&$spec) {

}

/**
 * Job.Synch API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_job_Sync($params) {

  // It is batch job, so it can run long
  set_time_limit(0);

  $config = CRM_Sync_Config::singleton();

  $merge = isset($params['merge']) ? $params['merge'] : 0;
  $limit = isset($params['limit']) ? $params['limit'] : 30;
  $sql = "select entity_id AS contact_id from civicrm_entity_tag 
          where  entity_table = 'civicrm_contact' 
          and    tag_id = %1
          limit  %2";

  $dao = CRM_Core_DAO::executeQuery($sql, array(
     1 => array($config->getSyncTagid(),'Integer'),
     2 => array($limit,'Integer')
  ));

  $count = 0;
  $errorcount = 0;
  $errors = array();

  while($dao->fetch()){
    $params = array();
    $params['contact_id']= $dao->contact_id;
    $params['merge']= $merge;
    try {
      $result = civicrm_api3('Sync', 'send', $params);
    } catch (Exception $ex)
    {
      $result = array ('is_error' => 1,
        'error_message' => $ex['error_message']);
    }
    if($result['is_error']){
      $errors[$dao->contact_id] = $result;
      $errorcount++;
    }
    $count++;
  }
  return civicrm_api3_create_success(array
    ('count' => $count,
     'errorcount' => $errorcount,
     'errors' => $errors),
    'Did run a synchronize job', 'Job', 'sync');
}
