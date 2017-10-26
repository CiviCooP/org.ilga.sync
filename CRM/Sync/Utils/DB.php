<?php
/**
 *  Helper functions to information.
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
   *  Find the contact id with the ilga id. This is only defined in the region
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

      return CRM_Core_DAO::singleValueQuery($sql, [
        '1' => [$ilgaId, 'Integer']
      ]);
    }
  }

  /**
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

  public static function findLocalId($ilgaId){

    $config = CRM_Sync_Config::singleton();
    if($config->get('ilgasync_destination')=='hq'){
      return $ilgaId;
    } else {
      CRM_Sync_Utils_DB::findContactId($ilgaId);
    }
  }

}