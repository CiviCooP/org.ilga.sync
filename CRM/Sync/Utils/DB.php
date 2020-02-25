<?php
/**
 *  Helper functions to find information in the database using a known id.
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 22-10-17 14:40
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Utils_DB {

  /**
   * Find the ilga id using the contact id. This makes sense in the region
   * where the ilga id is defined as custom field. If the ilga id is not found
   * a null is returned.
   *
   * @param $contactId
   *
   * @return null|string
   */
  public static function findIlgaId($contactId){

    $config = CRM_Sync_Config::singleton();

    $sql = "SELECT {$config->getIlgaIdentifierCustomFieldColumnName()}
            FROM   {$config->getSynchronizationCustomGroupTableName()}
            WHERE  entity_id = %1";

   return CRM_Core_DAO::singleValueQuery($sql,array(
      '1' => array($contactId,'Integer')
    ));
  }

  /**
   *  Find the contact id with the ilga id. This makes only sense
   *  in the region
   *  If the ilga id is not found an empty value is returned.
   *
   * @param $ilgaId
   *
   * @return null|string
   */
  public static function findContactId($ilgaId){

    if(empty($ilgaId)){
      return FALSE;
    } else {

      $config = CRM_Sync_Config::singleton();

      $sql = "SELECT entity_id
            FROM   {$config->getSynchronizationCustomGroupTableName()}
            WHERE  {$config->getIlgaIdentifierCustomFieldColumnName()} = %1";

      $result =  CRM_Core_DAO::singleValueQuery($sql, [
        '1' => [$ilgaId, 'Integer']
      ]);
      return $result;
    }
  }

  /**
   * Find the region of a contact (using the primary address).
   * But a contact does not always have region.
   * @param $contactId
   *
   * @return null|string
   */
  public static function findRegionId($contactId){

    $sql = 'SELECT cnt.region_id FROM civicrm_country cnt
            JOIN   civicrm_address adr ON (cnt.id = adr.country_id)
            WHERE  adr.contact_id = %1 AND adr.is_primary = 1';

    return CRM_Core_DAO::singleValueQuery($sql,array(
      '1' => array($contactId,'Integer')
    ));
  }

  /* Find the local contact id using the ilga id
     In the headquarters the ilga is the local id
  */
  /**
   * @param $ilgaId
   *
   * @return mixed
   * @throws \Exception
   */
  public static function findLocalId($ilgaId){
    $config = CRM_Sync_Config::singleton();
    if($config->get('ilgasync_destination')=='hq'){
      return $ilgaId;
    } else {
      CRM_Sync_Utils_DB::findContactId($ilgaId);
    }
  }

  /**
   * @param $contactId
   *
   * @return string
   */
  public static function findMembershipType($contactId){
    $sql = "select 1 from civicrm_membership m
            join civicrm_membership_type t on (m.membership_type_id = t.id)
            where m.contact_id = %1 and t.name like %2";

    $result = array();
    if(CRM_Core_DAO::singleValueQuery($sql,array(
      1 => array($contactId,'Integer'),
      2 => array('Full Membership%','String')
    ))){
      $result[] = 'Member_organisation';
    }
    if(CRM_Core_DAO::singleValueQuery($sql,array(
      1 => array($contactId,'Integer'),
      2 => array('Associate membership','String')
    ))){
      $result[] = 'Associated_members';
    }
    return implode(',',$result);
  }

  /**
   * @param $contactId
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  public static function isMember($contactId) {
    $result = civicrm_api3('Contact', 'getsingle', [
      'return' => ["contact_sub_type"],
      'id' => $contactId,
    ]);
    return $result['contact_sub_type'] && in_array('Member_organisation', $result['contact_sub_type']);
  }

}
