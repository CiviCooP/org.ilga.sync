<?php
/**
 * Maps all the configuration names on the technical keys
 * Uses singleton pattern to prevent to reduce database turnarounds
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 17-10-17 20:17
 * @license AGPL-3.0
 *
 */
class CRM_Sync_Config {

  /* singleton that stores all the vars */
  private static $_singleton;

  private $_ilgaIdentifierCustomFieldId;
  private $_synchronizationCustomGroupTableName;
  private $_ilgaIdentifierCustomFieldColumnName;
  private $_ilgaSyncSettings;
  private $_syncTagId;

  /**
   * @return integer
   */
  public function getIlgaIdentifierCustomFieldId() {
    return $this->_ilgaIdentifierCustomFieldId;
  }

  /**
   * @return string
   */
  public function getSynchronizationCustomGroupTableName() {
    return $this->_synchronizationCustomGroupTableName;
  }

  /**
   * @return string
   */
  public function getIlgaIdentifierCustomFieldColumnName() {
    return $this->_ilgaIdentifierCustomFieldColumnName;
  }

  /**
   * @return string
   */
  public function getSyncTagid() {
    return $this->_syncTagId;
  }

  /**
   * Constructor method: finds the ids by the names
   *
   * @param string $context
   */
  function __construct($context) {

    try {
      $this->_ilgaIdentifierCustomFieldId = civicrm_api3('CustomField', 'getvalue', [
        'return' => "id",
        'name' => "Ilga_identifier",
        'custom_group_id' => 'Synchronization',
      ]);
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field Ilga Identifier in' . __FILE__ . ' on line' . __LINE__);
    }
    try {
      $this->_synchronizationCustomGroupTableName = civicrm_api3('CustomGroup', 'getvalue', [
        'return' => "table_name",
        'name' => 'Synchronization',
      ]);
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Teammember_nr in' . __FILE__ . ' on line' . __LINE__);
    }
    try {
      $this->_ilgaIdentifierCustomFieldColumnName = civicrm_api3('CustomField', 'getvalue', [
        'return' => "column_name",
        'name' => "Ilga_identifier",
        'custom_group_id' => 'Synchronization',
      ]);
    } catch (Exception $ex) {
      throw new Exception('Could not find Custom Field  Teammember_nr in' . __FILE__ . ' on line' . __LINE__);
    }

    try {
      $this->_syncTagId = civicrm_api3('Tag', 'getvalue', [
        'return' => "id",
        'name' => "Sync",
      ]);
    } catch (Exception $ex) {
      throw new Exception('Could not find tagId in' . __FILE__ . ' on line' . __LINE__);
    }

    /* Although CiviCRM 4.7 has service to read settings directly, it cannot be used
       because the code must also work in 4.6. So it is red from the database */
    $dao = CRM_Core_DAO::executeQuery("select name,value from civicrm_setting WHERE name like 'ilga%'");
    while($dao->fetch()){
      $this->_ilgaSyncSettings[$dao->name] = unserialize($dao->value);
    }

  }

  public function get($setting){
    if(!array_key_exists($setting,$this->_ilgaSyncSettings)){
      throw new Exception('Ilga Sync Setting '.$setting.' is not found');
    }
    return $this->_ilgaSyncSettings[$setting];
  }

  /**
   * Singleton method
   *
   * @param string $context to determine if triggered from install hook
   *
   * @return CRM_Sync_Config
   * @access public
   * @static
   */
  public static function singleton($context = NULL) {
    if (!self::$_singleton) {
      self::$_singleton = new CRM_Sync_Config($context);
    }
    return self::$_singleton;
  }

}