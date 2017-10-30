<?php
/**
 *
 * <add a short description>
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 25-10-17 13:52
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Page_Send extends CRM_Core_Page {

  public function run() {

    $contactId = CRM_Utils_Request::retrieve('cid','Integer');
    $syncaction = CRM_Utils_Request::retrieve('syncaction','String');
    $config = CRM_Sync_Config::singleton();
    $region = $config->get('ilgasync_destination')=='region';

    switch($syncaction){

      case 'send' :
        $result = civicrm_api3('Sync','send',array('contact_id' => $contactId));
        break;

      case 'retrieve' :
        $remoteContact = CRM_Sync_Message::retrieve($contactId);
        CRM_Sync_Message::process($remoteContact);
        break;

    }

    $this->assign('action',$syncaction);

    if(CRM_Sync_Subscription::canSubscribe($contactId)) {
      $localContact  = CRM_Sync_Message::construct($contactId);
      $remoteContact = CRM_Sync_Message::retrieve($contactId);
      $this->assign('localContact', $localContact);
      $this->assign('sendUrl',CRM_Utils_System::url('civicrm/sync/send', "reset=1&cid={$contactId}&syncaction=send"));
      if($remoteContact){

        $mergeContact = CRM_Sync_Message::merge($remoteContact,$localContact);
        $this->assign('mergeContact',$mergeContact);


        $this->assign('retrieveUrl',CRM_Utils_System::url('civicrm/sync/send', "reset=1&cid={$contactId}&syncaction=retrieve"));
      }
      $this->assign('remoteContact', $remoteContact);
    } else {

    }
    parent::run();
  }

}
