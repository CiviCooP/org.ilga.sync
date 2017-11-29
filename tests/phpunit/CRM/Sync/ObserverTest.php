<?php
/**
 *
 * Tests for the observer class
 *
 *  @author Klaas Eikelboom (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 25-10-17 14:11
 *  @license AGPL-3.0
 *
 *  @group headless
 *
 */
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
class CRM_Sync_ObserverTest extends \PHPUnit_Framework_TestCase implements  HeadlessInterface {

  private $_contactId;

  public function setUpHeadless() {
  }

  public function setUp() {
    $result = civicrm_api3('contact','create',array(
      'contact_type' => 'Organization',
      'organization_name' => 'Diverse Negosie'
    ));
    $this->_contactId = $result['id'];
  }

  public function tearDown() {
    civicrm_api3('contact','delete',array(
      'id' => $this->_contactId,
      'skip_undelete' => 1,
    ));
  }

  public function testContactCreatedCorrect(){
    $this->assertNotEmpty($this->_contactId);
  }

  public function testTaggingUntagging(){
    $obs = new CRM_Sync_Observer();
    $this->assertEmpty($obs->tagged($this->_contactId));
    $obs->tag($this->_contactId);
    $this->assertNotEmpty($obs->tagged($this->_contactId));
    $obs->tag($this->_contactId);
    $obs->untag($this->_contactId);
    $this->assertEmpty($obs->tagged($this->_contactId));
    $obs->untag($this->_contactId);
  }
}