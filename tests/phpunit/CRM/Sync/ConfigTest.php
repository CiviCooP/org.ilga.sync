<?php
/**
 *
 * <add a short description>
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 25-10-17 10:02
 *  @license AGPL-3.0
 *
 *  @group headless
 *
 */

use Civi\Test\HeadlessInterface;

class CRM_Sync_ConfigTest extends \PHPUnit_Framework_TestCase implements  HeadlessInterface {

  public function setUpHeadless() {
  }

  public function testConfig(){
    $config = CRM_Sync_Config::singleton();
    $this->assertNotEmpty($config->getIlgaIdentifierCustomFieldId());
    $this->assertNotEmpty($config->getSynchronizationCustomGroupTableName());
    $this->assertNotEmpty($config->getIlgaIdentifierCustomFieldColumnName());
    $this->assertNotEmpty($config->getSyncTagid());

  }

}