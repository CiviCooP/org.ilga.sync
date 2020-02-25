<?php
/**
 * @author Klaas Eikelboom  <klaas.eikelboom@civicoop.org>
 * @date 25-Feb-2020
 * @license  AGPL-3.0
 */

class CRM_Sync_Job {

  /**
   * CRM_Sync_Job constructor.
   */
  public function __construct() {
  }

  /**
   *  Prepare the sync table for Europe
   */
  function europePrepare(){

    // Start with a known situation - so cleanup the table
    CRM_Core_DAO::executeQuery('delete from ilga_sync_contact');
    // insert all the contacts that Europe think are members
    $dao =CRM_Core_DAO::executeQuery('select id,display_name from civicrm_contact where contact_type = %1 and contact_sub_type is not null',[
        1 => ['Organization','String']
      ]
    );
    while($dao->fetch()){
      $contactId = $dao->id;
      if(CRM_Sync_Utils_DB::isMember($contactId)) {
        $ilgaId= CRM_Sync_Utils_DB::findIlgaId($contactId);
        $status = isset($ilgaId)?'REMOTE':'LOCAL';
        CRM_Core_DAO::executeQuery('insert into ilga_sync_contact(contact_id,status,display_name) values(%1,%2,%3)', [
            1 => [$contactId, 'Integer'],
            2 => [$status, 'String'],
            3 => [$dao->display_name,'String']
          ]
        );
        if($ilgaId){
          CRM_Core_DAO::executeQuery('update ilga_sync_contact set ilga_identifier = %1 where contact_id=%2',[
              1 => [$ilgaId,'Integer'],
              2 => [$contactId, 'Integer'],
           ]);
        }
      }
    }
  }

  /**
   * @throws \Exception
   */
  function europeSync(){
    $dao = CRM_Core_DAO::executeQuery('select contact_id, ilga_identifier from ilga_sync_contact where status=%1 limit 2' ,[
       1=>['REMOTE','String']
    ]);
    while($dao->fetch()){
      $message = CRM_Sync_Message::retrieve($dao->contact_id);
      print_r($message);
    }
  }

}
