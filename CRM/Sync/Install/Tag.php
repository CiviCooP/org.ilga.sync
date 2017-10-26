<?php
/**
 *  For the automatic installation of tags
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 25-10-17 10:12
 *  @license AGPL-3.0
 *
 */

class CRM_Sync_Install_Tag {

  public function create($tag){

    $tagId = CRM_Core_DAO::singleValueQuery('select id from civicrm_tag where name=%1',
      array(
        1 => array($tag,'String')
      ));

    $params = array (
      'name' => $tag,
      'used_for' => 'civicrm_contact',
      'is_selectable' => 1,
      'is_reserved' => 1,
      'description' => 'Contact is candidate for synchronization'
    );

    if($tagId){
      $params['id'] = $tagId;
    }
    civicrm_api3('Tag','create',$params);
  }

}