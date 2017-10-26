<?php
/**
 * Helper methods to construct and process the synchronization message
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 17-10-17 20:17
 * @license AGPL-3.0
 *
 */
class CRM_Sync_Message {

  static $_addressfields = [
    'street_address',
    'supplemental_address_1',
    'supplemental_address_2',
    'city',
    'postal_code',
    'country_id'

  ];

  private static function completeAndRestrict($params,$fields) {
    $result = [];
    foreach ($fields as $field) {
      if (array_key_exists($field, $params)) {
        $result[$field] = $params[$field];
      }
      else {
        $result[$field] = "";
      }
    }
    return $result;
  }

  public static function addressSame($address1,$address2){
    return CRM_Sync_Message::arraySame($address1,$address2,CRM_Sync_Message::$_addressfields);
  }

  public static function messageSame($message1,$message2){
    return CRM_Sync_Message::arraySame($message1,$message2,array());
  }

  private static function arraySame($address1,$address2,$fields) {

    if(empty($address1)&&empty($address2)){
      return TRUE;
    } elseif (isset($address1)&&empty($address2)){
      return FALSE;
    } elseif (empty($address1)&&isset($address2)){
      return FALSE;
    } else {
      $result = TRUE;
      $la1 = CRM_Sync_Message::completeAndRestrict($address1, $fields);
      $la2 = CRM_Sync_Message::completeAndRestrict($address2, $fields);
      foreach(CRM_Sync_Message::$_addressfields as $field){
        if($la1[$field]!=$la2[$field]){
          $result = FALSE;
        }
      }
      return $result;
    }
  }


  /**
   * @param $contactId
   * @param $websitetype
   *
   * @return bool
   */
  static function readWebsite($contactId, $websitetype) {
    $result = civicrm_api3('website', 'get', [
      'contact_id' => $contactId,
      'website_type_id' => $websitetype,
    ]);

    if ($result['count'] == 0) {
      return FALSE;
    }
    elseif ($result['count'] == 1) {
      return $result['values'][$result['id']];
    }
    else {
      $values = $result['values'];
      return current($values);
    }
  }

  static function writeWebsite($contactId, $websitetype,$url){
    $local = CRM_Sync_Message::readWebsite($contactId,$websitetype);
    if($local) {
      if ($url) {
        if ($url != $local['url']) {
          civicrm_api3('website', 'create', [
            'id' => $local['id'],
            'url' => $url
          ]);
        }
        else {
          // urls are the same - do nothing
        }
      }
      else { // got an empty url, but we have a local url - so delete
        civicrm_api3('website', 'delete', [
          'id' => $local['id']
        ]);
      }
    } else { // no local - we can insert
      if ($url) {
        civicrm_api3('website', 'create', [
          'contact_id' => $contactId,
          'url' => $url,
          'website_type_id' => $websitetype,
        ]);
      } else {
        // no local url and no url nothing to do
      }
    }
  }

  /**
   * @param $contactId
   *
   * @return bool
   */
  public static function readPhone($contactId) {
    $result = civicrm_api3('phone', 'get', [
      'contact_id' => $contactId,
      'is_primary' => 1,
    ]);

    if ($result['count'] == 0) {
      return FALSE;
    }
    else  {
      return $result['values'][$result['id']];
    }
  }

  public static function writePhone($contactId,$phone) {
    $config = CRM_Sync_Config::singleton();
    $local = CRM_Sync_Message::readPhone($contactId, $phone);
    if ($local) {
      if ($phone) {
        if ($phone != $local['phone']) {
          civicrm_api3('phone', 'create', [
            'id' => $local['id'],
            'phone' => $phone
          ]);
        }
        else {
          // its the same - do nothing
        }
      }
      else { // phone is empty so delete
        civicrm_apir('phone', 'delete', ['id' => $local['id']]);
      }
    }
    else {
      if ($phone) { // no local and and phone - go create
        civicrm_api3('phone', 'create', [
          'contact_id' => $contactId,
          'phone' => $phone,
          'is_primary' => 1,
          'phone_type_id' => $config->get('ilgasync_default_phone')
        ]);
      }
      else {
        // everything empty - do nothing
      }
    }
  }

  /**
   * @param $contactId
   *
   * @return bool
   */
  public static function readEmail($contactId) {
    $result = civicrm_api3('email', 'get', [
      'contact_id' => $contactId,
      'is_primary' => 1,
    ]);

    if ($result['count'] == 0) {
      return FALSE;
    }
    else  {
      return $result['values'][$result['id']];
    }
  }

  public static function writeEmail($contactId,$email) {
    $config = CRM_Sync_Config::singleton();
    $local = CRM_Sync_Message::readEmail($contactId, $email);
    if ($local) {
      if ($email) {
        if ($email != $local['email']) {
          civicrm_api3('email', 'create', [
            'id' => $local['id'],
            'phone' => $email
          ]);
        }
        else {
          // its the same - do nothing
        }
      }
      else { // phone is empty so delete
        civicrm_api3('email', 'delete', ['id' => $local['id']]);
      }
    }
    else {
      if ($email) { // no local and and phone - go create
        civicrm_api3('email', 'create', [
          'contact_id' => $contactId,
          'email' => $email,
          'is_primary' => 1,
          'phone_type_id' => $config->get('ilgasync_default_email')
        ]);
      }
      else {
        // everything empty - do nothing
      }
    }
  }

  private static function readAddress($contactId){

    $result = civicrm_api3('Address', 'get', array(
      'contact_id' => $contactId,
      'is_primary' => 1,
    ));

    if ($result['count'] == 0) {
      return FALSE;
    }
    else  {
      return $result['values'][$result['id']];
    }
  }

  private static function writeAddress($contactId, $address) {
    $config = CRM_Sync_Config::singleton();
    $local = CRM_Sync_Message::readAddress($contactId);
    if ($local) {
      if ($address) {
        if (!CRM_Sync_Message::addressSame($address, $local)) {
          civicrm_api3('Address', 'create', array('id' => $local['id']) + $address);
        }
        else {
          // the same so no change
        }
      }
      else {
        civicrm_api3('Address', 'delete', ['id' => $local['id']]);
      }
    }
    else {
      if ($address) {
        civicrm_api3('Address', 'create', [
            'contact_id' => $contactId,
            'location_type_id' => $config->get('ilgasync_default_address'),
          ] + $address);

      }
      else {
        // no local no remote do nothing
      }
    }
  }


  /**
   * @param $contactId
   * @param $destination
   *
   * @return array
   */
  static public function construct($contactId, $destination) {

    $config = CRM_Sync_Config::singleton();
    if(!in_array($destination,['hq','region'])){
       throw Exception("Cannot construct a message for unknown destination ".$destination);
    }

    $message=array();
    $message['destination']=$destination;

    if($destination=='hq'){
      $message['ilga_identifier']=$contactId;
    } else {
      $message['ilga_identifier']=CRM_Sync_Utils_DB::findIlgaId($contactId);
    }

    $result = civicrm_api3('contact','getsingle',array(
      'id' => $contactId,
    ));

    $message = $message+CRM_Utils_Array::subset($result,[
        'organization_name',
        'legal_name',
        'nick_name',
        'preferred_language',
        'contact_type',
       // 'contact_sub_type',
        'is_opt_out'
      ]);

    $message['email'] = CRM_Sync_Message::readEmail($contactId)['email'];
    $message['phone'] = CRM_Sync_Message::readPhone($contactId)['phone'];
    $message['website']  = CRM_Sync_Message::readWebsite($contactId,$config->get('ilgasync_default_website'))['url'];
    $message['facebook'] =  CRM_Sync_Message::readWebsite($contactId,$config->get('ilgasync_default_facebook'))['url'];
    $address = CRM_Sync_Message::readAddress($contactId);
    if($address) {
      $message['address'] = CRM_Sync_Message::completeAndRestrict($address, CRM_Sync_Message::$_addressfields);
    }
    return $message;
  }

  /**
   * Processes an incoming message. At the moment only the update is
   * supported
   *
   * @param $message
   */
  static public function process($message){

    $config = CRM_Sync_Config::singleton();

    if($message['destination']=='hq'){
      // the contact id of the head quarters are the ilga identifier
      $contactId = $message['ilga_identifier'];
    } elseif($message['destination']=='region') {
      // in the region the contact id must be found with the ilga identifier.
      $contactId = CRM_Sync_Utils_DB::findContactId($message['ilga_identifier']);
    } else {
      throw new Exception ("Message does not have a valid destination (hq or region)");
    }

    $contactParams = array();
    if($contactId) {
      $contactParams['id'] = $contactId;
    } else {
      $contactParams['custom_'.$config->getIlgaIdentifierCustomFieldId()] = $message['ilga_identifier'] ;
    }
    $contactParams = $contactParams + CRM_Utils_Array::subset($message,[
      'organization_name',
      'legal_name',
      'nick_name',
      'preferred_language',
      'contact_type',
      'contact_sub_type',
      'is_opt_out'
    ]);

    $result = civicrm_api3('contact','create',$contactParams);
    $contactId = $result['id'];

    CRM_Sync_Message::writeWebsite($contactId,$config->get('ilgasync_default_facebook'),$message['facebook']);
    CRM_Sync_Message::writeWebsite($contactId,$config->get('ilgasync_default_website'),$message['website']);
    CRM_Sync_Message::writeEmail($contactId,$message['email']);
    CRM_Sync_Message::writePhone($contactId,$message['phone']);
    CRM_Sync_Message::writeAddress($contactId,$message['address']);

  }

  /**
   * @param $ilgaId
   *
   * @return mixed
   * @throws \Exception
   */
  static public function retrieve($contactId,$destination){

    if($destination=='hq'){
      $ilgaId = CRM_Sync_Utils_DB::findIlgaId($contactId);
    } else {
      $ilgaId=$contactId;
    }

    $params =[
      'destination' => $destination,
      'ilga_identifier' => $ilgaId
    ];

    $result = CRM_Sync_Utils_Rest::call('retrieve',$params);

    if($result['is_error']){
      throw new Exception('Remote Error:' . print_r($result,true));
    };

    if($result['values']['found']) {
      return json_decode($result['values']['payload'], TRUE);
    } else {
      return FALSE;
    }
  }

  static public function merge($hq,$region){
    $result = array();
    $result['ilga_identifier'] = $region['ilga_identifier'];
    $result['organization_name'] = $region['organization_name'];
    $result['legal_name'] = $region['legal_name'];
    $result['nick_name']  = $region['nick_name'];
    $result['email'] = $hq['email'];
    $result['website'] = $region['website'];
    $result['phone'] = 'Europe/World?';
    $result['facebook'] = 'Europe/World?';
    $result['is_opt_out'] = 'Europe/World?';
    $result['preferred_language'] = 'Europe/World?';
    $result['address']= isset($region['address'])? $region['address'] : $hq['address'];
    return $result;
  }

}