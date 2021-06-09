<?php


namespace Services\Tests;


use Services\AccountsService;
use Services\General\iAccount;
use Services\General\iModule;

require_once __DIR__ . '/../TestFactory.php';

class AccountsServiceTest extends \PHPUnit\Framework\TestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTests();
	}

	function testGetSelf(){
		$service = AccountsService::getSelf();
		$this->assertInstanceOf(AccountsService::class, $service);
	}

	function testFindAmocrmById() {
		$accounts = TestAccounts::createUniqueAccounts();
		$answer = TestFactory::getAccountsService()->findAmocrmById($accounts[0]->getAmocrmAccountId());
		$this->assertInstanceOf(iAccount::class, $answer);
		$this->compareStructs($accounts[0], $answer);
	}

	function testFindAmocrmBySubdomain(){
		$accounts = TestAccounts::createUniqueAccounts();
		$answer = TestFactory::getAccountsService()->findAmocrmBySubdomain($accounts[0]->getAmocrmSubdomain());
		$this->assertInstanceOf(iAccount::class, $answer);
		$this->compareStructs($accounts[0], $answer);
	}

	function testFindAmocrmByReferer(){
		$accounts = TestAccounts::createUniqueAccounts();
		$answer = TestFactory::getAccountsService()->findAmocrmByReferer($accounts[0]->getAmocrmReferer());
		$this->assertInstanceOf(iAccount::class, $answer);
		$this->compareStructs($accounts[0], $answer);
	}

	function testGetAccount(){
		$accounts = TestAccounts::createUniqueAccounts();
		$answer = TestFactory::getAccountsService()->getAccount($accounts[0]->getPragmaAccountId());
		$this->assertInstanceOf(iAccount::class, $answer);
		$this->compareStructs($accounts[0], $answer);
	}

	function compareStructs(iAccount $acc1, iAccount $acc2): void {
		$this->assertEquals($acc1->getDomain(), $acc2->getDomain());
		$this->assertEquals($acc1->getBitrix24Referer(), $acc2->getBitrix24Referer());
		$this->assertEquals($acc1->getAmocrmReferer(), $acc2->getAmocrmReferer());
		$this->assertEquals($acc1->getAmocrmAccountId(), $acc2->getAmocrmAccountId());
		$this->assertEquals($acc1->getPragmaAccountId(), $acc2->getPragmaAccountId());
		$this->assertEquals($acc1->getBitrix24MemberId(), $acc2->getBitrix24MemberId());
		$this->assertEquals($acc1->getAmocrmCountry(), $acc2->getAmocrmCountry());
		$this->assertEquals($acc1->getAmocrmCreatedByUserId(), $acc2->getAmocrmCreatedByUserId());
		$this->assertEquals($acc1->getAmocrmCreateTime(), $acc2->getAmocrmCreateTime());
		$this->assertEquals($acc1->getAmocrmName(), $acc2->getAmocrmName());
		$this->assertEquals($acc1->getAmocrmSubdomain(), $acc2->getAmocrmSubdomain());
		$this->assertEquals($acc1->getBitrix24Lang(), $acc2->getBitrix24Lang());
		$this->assertEquals($acc1->getCrmName(), $acc2->getCrmName());
		$this->assertEquals($acc1->getPragmaTimeCreate(), $acc2->getPragmaTimeCreate());
	}
}