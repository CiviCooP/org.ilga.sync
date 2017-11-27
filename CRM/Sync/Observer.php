<?php
/**
 *  Observers changes and marks them when the record must be send.
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 25-10-17 10:53
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Observer {

  private static $_cache;

  public static function cache() {
    if (!self::$_cache) {
      self::$_cache = array(
        'contact' => array(),
        'email'   => array(),
        'website' => array(),
      );
    }
    return self::$_cache;
  }

  public function storeEmail($id,$email){
    $cache = CRM_Sync_Observer::cache();
    $cache['email'][$id]=$email;
  }

  public function emailChanged($id,$email){
    $cache = CRM_Sync_Observer::cache();
    return $cache['email'][$id]==$email;
  }

  /**
   * @param $contactId
   */
  public function tag($contactId){
    if(empty($this->tagged($contactId))) {
      $config = CRM_Sync_Config::singleton();
      civicrm_api3('EntityTag', 'create', [
        'entity_id' => $contactId,
        'entity_table' => "civicrm_contact",
        'tag_id' => $config->getSyncTagid(),
      ]);
    }
  }

  /**
   * @param $contactId
   */
  public function untag($contactId) {
    if (!empty($this->tagged($contactId))) {
      $config = CRM_Sync_Config::singleton();
      civicrm_api3('EntityTag', 'delete', [
        'entity_id' => $contactId,
        'entity_table' => "civicrm_contact",
        'tag_id' => $config->getSyncTagid(),
      ]);
    }
  }

  public function tagged($contactId){
    $config = CRM_Sync_Config::singleton();
    $sql = "select 1 from civicrm_entity_tag 
            where  tag_id = %1 
            and    entity_id = %2
            and    entity_table = 'civicrm_contact'";
    return CRM_Core_DAO::singleValueQuery($sql,array(
      1 => array($config->getSyncTagid(),'Integer'),
      2 => array($contactId,'Integer')
    ));
  }

  /**
   * Check if a contact is changed
   * @param $contactId
   */
  public function observeContact($contactId){
    if($this->subscribed($contactId)) {
      $this->tag($contactId);
    }
  }

  /**
   * @param $phoneId
   */
  public function observePhone($phoneId){

  }

  /**
   * if a email is changed it might be send over.
   * if
   *    - it is really changed
   *    - it connected to a contact that must be synchronized
   *  if it is changed the connected contact must be marked with
   *  tag
   *
   * @param $emailId
   */
  public function observeEmail($emailObject){
    $contactId = $emailObject->contact_id;
    if(isset($contactId) && $this->subscribed($contactId)) {
      $this->tag($contactId);
    }
  }

  /**
   * @param $websiteId
   */
  public function observeWebsite($websiteObject){
    $contactId = $websiteObject->contact_id;
    if(isset($contactId) && $this->subscribed($contactId)) {
      $this->tag($contactId);
    }
  }

  public function observeAddress($addressObject){
    $contactId = $addressObject->contact_id;
    if(isset($contactId) && $this->subscribed($contactId)) {
      $this->tag($contactId);
    }
  }

  public function subscribed($contactId){
    $config = CRM_Sync_Config::singleton();
    $region = $config->get('ilgasync_destination')=='region';
    if($region){
      if(CRM_Sync_Utils_DB::findIlgaId($contactId)){
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      $regionId = CRM_Sync_Utils_DB::findRegionId($contactId);
      return $regionId == $config->get('ilgasync_region');
    }

  }





}