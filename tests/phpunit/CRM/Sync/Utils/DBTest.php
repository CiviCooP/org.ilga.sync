<?php
/**
 *
 * <add a short description>
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 28-11-17 17:04
 *  @license AGPL-3.0
 *
 *  @group headless
 *
 */

use Civi\Test\HeadlessInterface;
class CRM_Sync_Utils_DBTest extends \PHPUnit_Framework_TestCase implements  HeadlessInterface {

  private $_contactId;

  public function setUpHeadless() {
  }

  public function setUp(){
    $config = CRM_Sync_Config::singleton();
    $result = civicrm_api3('contact','create',array(
      'contact_type' => 'Organization',
      'organization_name' => 'Diverse Negosie',
      'custom_'.$config->getIlgaIdentifierCustomFieldId() => 678912
    ));
    $this->_contactId = $result['id'];
  }

  public function tearDown(){
    civicrm_api3('contact','delete',array(
      'id' => $this->_contactId,
      'skip_undelete' => 1,
    ));
    // after test - return to headquarters
    $config = CRM_Sync_Config::singleton();
    $config->setForTest('ilgasync_destination','hq');
  }

  public function testFindIlgaId(){
    $config = CRM_Sync_Config::singleton();
    $config->setForTest('ilgasync_destination','hq');
    $this->assertEquals(CRM_Sync_Utils_DB::findIlgaId($this->_contactId),678912);
    // organization one, the owning organization has no ilgaid
    $this->assertNull(CRM_Sync_Utils_DB::findIlgaId(1));
    $config->setForTest('ilgasync_destination','region');
    $this->assertEquals(CRM_Sync_Utils_DB::findIlgaId($this->_contactId),678912);
    // organization one, the owning organization has no ilgaid
    $this->assertNull(CRM_Sync_Utils_DB::findIlgaId(1));
  }

  public function testFindContactId(){
    $config = CRM_Sync_Config::singleton();
    $config->setForTest('ilgasync_destination','hq');
    $this->assertEquals(CRM_Sync_Utils_DB::findContactId(678912),$this->_contactId);
    // organization one, the owning organization has no contactId
    $this->assertNull(CRM_Sync_Utils_DB::findContactId(1));
    $config->setForTest('ilgasync_destination','region');
    $this->assertEquals(CRM_Sync_Utils_DB::findContactId(678912),$this->_contactId);
    // organization one, the owning organization has no contactId
    $this->assertNull(CRM_Sync_Utils_DB::findContactId(1));
  }




}