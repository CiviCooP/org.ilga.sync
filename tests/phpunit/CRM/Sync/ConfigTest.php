<?php
/**
 *  Testing the configuration object
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

  public function tearDown(){
    // after test - return to headquarters
    $config = CRM_Sync_Config::singleton();
    $config->setForTest('ilgasync_destination','hq');
  }

  public function testConfig(){
    $config = CRM_Sync_Config::singleton();
    $this->assertNotEmpty($config->getIlgaIdentifierCustomFieldId());
    $this->assertNotEmpty($config->getSynchronizationCustomGroupTableName());
    $this->assertNotEmpty($config->getIlgaIdentifierCustomFieldColumnName());
    $this->assertNotEmpty($config->getSyncTagid());
  }

  public function testSettings(){
    $config = CRM_Sync_Config::singleton();
    // assumption is that the standard development database is in the hq
    // here we test for it.
    $this->assertEquals($config->get('ilgasync_destination'),'hq','Test must be run in HeadQuarters');
    // however, it must be possible in the test to simulate the region
    // so switch to region and we test.
    $config->setForTest('ilgasync_destination','region');
    $this->assertEquals($config->get('ilgasync_destination'),'region','Test must be run in Region');

    // both config get and set should work only for official ilga settings
    // here we check if the exception is thrown.
    try {
      $config->get('unknown_ilga_setting');
      $config->setForTest('unknown_ilga_setting','justavalue');
      throw new Exception('Oops, passed config->get and config->setForTest with no Exception');
    } catch (Exception $ex){
      // exception should be thrown - do nothing
    }

    // check of the used settings are configured, just by getting them all

    $settings = [
      'ilgasync_destination',
      'ilgasync_region',
      'ilgasync_url',
      'ilgasync_apikey',
      'ilgasync_default_address',
      'ilgasync_default_phone',
      'ilgasync_default_email',
      'ilgasync_default_website',
    ];
    foreach ($settings as $setting) {
      $this->assertNotNull($config->get($setting));
    }

  }

}