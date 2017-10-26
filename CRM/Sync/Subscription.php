<?php
/**
 *  Checks from a contactId if it can be sent to the other instance
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 20-10-17 18:07
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Subscription {

  static public function canSubscribe($contactId){
    $config = CRM_Sync_Config::singleton();
    $destination = $config->get('ilgasync_destination');
    switch($destination){
      case 'hq' : return CRM_Sync_Subscription::isHqSubScribed($contactId);
        break;
      case 'region' : return CRM_Sync_Subscription::canRegioSubScribe($contactId);
        break;
      default: throw new Exception("Unknown destination {$destination} in ".__CLASS__." function".__METHOD__);
    }

  }

  static public function isSubscribed($contactId){
     $config = CRM_Sync_Config::singleton();
     $destination = $config->get('ilgasync_destination');
     switch($destination){
       case 'hq' : return CRM_Sync_Subscription::isHqSubScribed($contactId);
                   break;
       case 'region' : return CRM_Sync_Subscription::isRegionSubScribed($contactId);
                       break;
       default: throw new Exception("Unknown destination {$destination} in ".__CLASS__." function".__METHOD__);
     }
  }

  static private function isHqSubScribed($contactId){
    $config = CRM_Sync_Config::singleton();
    $regionId = CRM_Sync_Utils_DB::findRegionId($contactId);
    if(isset($regionId) && $regionId==$config->get('ilgasync_region')){
      return TRUE;
    } else {
      return FALSE;
    }
  }

  static private function canRegioSubScribe($contactId){
    $config = CRM_Sync_Config::singleton();
    $regionId = CRM_Sync_Utils_DB::findRegionId($contactId);
    if(isset($regionId) && $regionId==$config->get('ilgasync_region')){
      return TRUE;
    } else {
      return FALSE;
    }
  }

  static private function isRegionSubScribed($contactId){
    $ilgaId = CRM_Sync_Utils_DB::findIlgaId($contactId);
    if(isset($ilgaId)){
      return TRUE;
    } else {
      return FALSE;
    }
  }
}