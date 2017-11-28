<?php
/**
 *
 * Test sets used to test certain asspects of the message class
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 24-10-17 12:34
 *  @license AGPL-3.0
 *
 *  @group headless
 *
 */

use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;

class CRM_Sync_MessageTest extends \PHPUnit_Framework_TestCase implements  HeadlessInterface {

    public function setUpHeadless() {
    }

    public function testAddressSame() {

      $address1 = [];
      $address1['street_address'] = 'Dillenburglaan 2';
      $address2 = [];
      $address2['street_address'] = 'Dillenburglaan 2';
      $this->assertTrue(CRM_Sync_Message::addressSame(FALSE, FALSE,'Both empty means no change should be the same'));
      $this->assertFalse(CRM_Sync_Message::addressSame($address1, FALSE,'Only one empty - thats a difference'));
      $this->assertFalse(CRM_Sync_Message::addressSame(FALSE, $address2,'Only one empty - thats a difference'));
      $this->assertTrue(CRM_Sync_Message::addressSame($address1, $address2,'Two addresses with same field, thats the same'));

      $address1['unimporant field']='just some value';

      $this->assertTrue(CRM_Sync_Message::addressSame($address1, $address2,'Added unimporant field should not matter'));
      $this->assertTrue(CRM_Sync_Message::addressSame($address2, $address1,'Added unimporant field should not matter'));

      $address2['street_address'] = 'Dillenburglaan 3';

      $this->assertFalse(CRM_Sync_Message::addressSame($address1, $address2,'But a different address should matter'));
      $this->assertFalse(CRM_Sync_Message::addressSame($address2, $address1,'But a different address should matter'));

      $address2['street_address'] = 'Dillenburglaan 2';
      $this->assertTrue(CRM_Sync_Message::addressSame($address1, $address2,'Two addresses with same field, thats the same'));

      $address1['city'] = 'Culemborg';

      $this->assertFalse(CRM_Sync_Message::addressSame($address2, $address1,'An extra field - does matter'));
    }

    public function testDiff(){

      $old['organization_name']='Oud';
      $new['organization_name']='New';

      $this->assertEquals(CRM_Sync_Message::diff($old,$new),"<p>organization_name was Oud and becomes New</p>");
      $new['organization_name']='';
      $this->assertEquals(CRM_Sync_Message::diff($old,$new),"<p>organization_name was Oud and becomes </p>");
      $new['organization_name']='Oud';
      $this->assertEquals(CRM_Sync_Message::diff($old,$new),"");
      $new['organization_name']=null;
      $this->assertEquals(CRM_Sync_Message::diff($old,$new),"<p>organization_name was Oud and becomes </p>");
      $old['organization_name']=null;
      $this->assertEquals(CRM_Sync_Message::diff($old,$new),"");
    }


}
