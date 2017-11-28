<?php
/**
 *  Checks from a contactId if it can be sent to the other instance
 *  if it is possible it can be subscribed.
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 20-10-17 18:07
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Subscription {

  /* this is the want situation is it allowed to describe this contact */
  static public function canSubscribe($contactId){
    $config = CRM_Sync_Config::singleton();
    $destination = $config->get('ilgasync_destination');
    /* behaviour is different in world als in region */
    switch($destination){
      case 'hq' : return CRM_Sync_Subscription::isHqSubScribed($contactId);
        break;
      case 'region' : return CRM_Sync_Subscription::canRegioSubScribe($contactId);
        break;
      /* outside europe and world, no subscription possible */
      default: throw new Exception("Unknown destination {$destination} in ".__CLASS__." function".__METHOD__);
    }
  }

  /* this is the actual situation, is the contact subscribed */
  static public function isSubscribed($contactId){
     $config = CRM_Sync_Config::singleton();
     $destination = $config->get('ilgasync_destination');
     /* behaviour is different in world als in region */
     switch($destination){
       case 'hq' : return CRM_Sync_Subscription::isHqSubScribed($contactId);
                   break;
       case 'region' : return CRM_Sync_Subscription::isRegionSubScribed($contactId);
                       break;
       /* outside europe and world, no subscription possible */
       default: throw new Exception("Unknown destination {$destination} in ".__CLASS__." function".__METHOD__);
     }
  }

  /*
     this is for world - does the contact has a region and
     is it the same as the configured region
  */
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

  /* in the region (europe) you are subscribed when you hava
     an ilga id
  */
  static private function isRegionSubScribed($contactId){
    $ilgaId = CRM_Sync_Utils_DB::findIlgaId($contactId);
    if(isset($ilgaId)){
      return TRUE;
    } else {
      return FALSE;
    }
  }
}