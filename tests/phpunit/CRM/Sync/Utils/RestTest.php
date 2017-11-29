<?php
/**
 *
 *  Test for the restcall (only the mock option)
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 28-11-17 20:41
 *  @license AGPL-3.0
 *
 *  @group headless
 *
 */

use Civi\Test\HeadlessInterface;
class CRM_Sync_Utils_RestTest extends \PHPUnit_Framework_TestCase implements  HeadlessInterface{

  public function setUpHeadless() {
  }

  public function testEmptyAtStart(){
    $mock = CRM_Sync_Utils_Rest::getMock();
    $this->assertEquals(count($mock),0);
  }

  public function testMockCall(){

    $params['mock'] = 1;
    $result = CRM_Sync_Utils_Rest::call('toDo',$params);

    $this->assertEquals($result,'mocked','A mock call should return mocked');

    $mock = CRM_Sync_Utils_Rest::getMock();
    $this->assertEquals(count($mock),1,' after one call, one record in the mock stack');
    $row = $mock[0];
    $this->assertArrayHasKey('url',$row,' in the mocked row an url must be stored');
    $this->assertArrayHasKey('action',$row, 'the mocked row has key action');
    $this->assertArrayHasKey('params',$row, 'it contains a key params');

    $url = $row['url'];

    $config = CRM_Sync_Config::singleton();
    // using the ! as delimeter because / works not very good in an url
    $this->assertRegExp("!{$config->get('ilgasync_url')}!",$url);
    $this->assertRegExp("!{$config->get('ilgasync_sitekey')}!",$url);
    $this->assertRegExp("!{$config->get('ilgasync_apikey')}!",$url);
    $this->assertRegExp("!mock!",$url);
  }

  public function tearDown(){
    CRM_Sync_Utils_Rest::forget();
  }

}