<?php


namespace Services\Tests;


use Services\Factory;
use Services\General\iAccount;
use Services\General\iModule;
use Services\General\iNode;
use Services\General\iUser;
use Services\NodesService;

require_once __DIR__ . '/../TestFactory.php';

class NodesServiceTest extends \PHPUnit\Framework\TestCase {
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTests();
	}

	function testGetSelf(){
		$service = NodesService::getSelf();
		$this->assertInstanceOf(NodesService::class, $service);
	}

	function testFindAmocrmNode(){
		$testNodes = TestNodes::createUniqueNodes();
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$this->_testFindAmocrmNode($testNodes);
		$this->_testFindAmocrmNode($withUsers);
	}

	private function _testFindAmocrmNode(array $testNodes): void {
		$answer = TestFactory::getNodesService()->findAmocrmNode($testNodes[0]->getModule()->getAmocrmIntegrationId(), $testNodes[0]->getAccount()->getAmocrmSubdomain());
		$this->compareNodes($testNodes[0], $answer);
	}

	function testFindAmocrmNodeCode(){
		$testNodes = TestNodes::createUniqueNodes();
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$this->_testFindAmocrmNodeCode($testNodes);
		$this->_testFindAmocrmNodeCode($withUsers);
	}

	private function _testFindAmocrmNodeCode(array $testNodes){
		$answer = TestFactory::getNodesService()->findAmocrmNodeCode($testNodes[0]->getModule()->getCode(), $testNodes[0]->getAccount()->getAmocrmSubdomain());
		$this->compareNodes($testNodes[0], $answer);
	}

	function testFindAmocrmNodeAccId(){
		$testNodes = TestNodes::createUniqueNodes();
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$this->_testFindAmocrmNodeAccId($testNodes);
		$this->_testFindAmocrmNodeAccId($withUsers);
	}

	private function _testFindAmocrmNodeAccId(array $testNodes){
		$answer = TestFactory::getNodesService()->findAmocrmNodeAccId($testNodes[0]->getModule()->getCode(), $testNodes[0]->getAccount()->getAmocrmAccountId());
		$this->compareNodes($testNodes[0], $answer);
	}

	function testFindPragmaNode(){
		$testNodes = TestNodes::createUniqueNodes();
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$this->_testFindPragmaNode($testNodes);
		$this->_testFindPragmaNode($withUsers);
	}

	private function _testFindPragmaNode(array $testNodes): void {
		$answer = TestFactory::getNodesService()->findPragmaNode($testNodes[0]->getModule()->getCode(), $testNodes[0]->getAccount()->getPragmaAccountId());
		$this->compareNodes($testNodes[0], $answer);
	}

	function testCreateInactiveApiKey(){
		$testUsers = TestUsers::createUniqueUsers();
		$testNodes = TestNodes::createUniqueNodes();
		$answer = $testNodes[0]->createInactiveApiKey($testUsers[0]->getPragmaUserId());
		$this->assertIsString($answer);
	}

	function testCheckInactiveApiKey(){
		$testUsers = TestUsers::createUniqueUsers();
		$testNodes = TestNodes::createUniqueNodes();
		$token = $testNodes[0]->createInactiveApiKey($testUsers[0]->getPragmaUserId());
		$flag = $testNodes[0]->checkApiKey($token);
		$this->assertFalse($flag);
	}

	function testSetShutdownTime(){
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$withUsers[0]->setShutdownTime(time() + 3600 * 3);
		$actual0 = TestNodes::getNode($withUsers[0]->getModule(), $withUsers[0]->getAccount(), $withUsers[0]->getUser());
		$actual1 = TestFactory::getNodesService()->findPragmaNode($withUsers[0]->getModule()->getCode(), $withUsers[0]->getAccount()->getPragmaAccountId());
		$this->compareNodes($withUsers[0], $actual0);
		$this->compareNodes($withUsers[0], $actual1);
	}

	function testSetAmocrmDisable(){
		$withUsers = TestNodes::createUniqueNodesWithUsers();
		$this->assertTrue($withUsers[0]->isAmocrmEnable());
		$withUsers[0]->setAmocrmDisable();
		$actual0 = TestNodes::getNode($withUsers[0]->getModule(), $withUsers[0]->getAccount(), $withUsers[0]->getUser());
		$this->assertFalse($withUsers[0]->isAmocrmEnable());
		$this->assertFalse($actual0->isAmocrmEnable());
	}

	private function compareNodes(iNode $node0, iNode $node1): void {
		$this->compareNodesFields($node0, $node1);
		$this->compareAccounts($node0->getAccount(), $node1->getAccount());
		$this->compareModules($node0->getModule(), $node1->getModule());
		$this->compareUsers($node0->getUser(), $node1->getUser());
	}

	private function compareNodesFields(iNode $node0, iNode $node1): void {
		$this->assertEquals($node0->getPragmaUserId(), $node1->getPragmaUserId());
		$this->assertEquals($node0->getShutdownTime(), $node1->getShutdownTime());
		$this->assertEquals($node0->isAmocrmEnable(), $node1->isAmocrmEnable());
		$this->assertEquals($node0->isOnceInstalled(), $node1->isOnceInstalled());
		$this->assertEquals($node0->isPragmaActive(), $node1->isPragmaActive());
		$this->assertEquals($node0->isUnlimited(), $node1->isUnlimited());
	}

	private function compareAccounts(iAccount $acc1, iAccount $acc2): void {
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

	private function compareModules(iModule $module0, iModule $module1): void {
		$this->assertEquals($module0->getAmocrmIntegrationId(), $module1->getAmocrmIntegrationId());
		$this->assertEquals($module0->getPragmaModuleId(), $module1->getPragmaModuleId());
		$this->assertEquals($module0->getFreePeriodDays(), $module1->getFreePeriodDays());
		$this->assertEquals($module0->getCode(), $module1->getCode());
		$this->assertEquals($module0->getBitrix24IntegrationId(), $module1->getBitrix24IntegrationId());
		$this->assertEquals($module0->getAmocrmCode(), $module1->getAmocrmCode());
	}

	private function compareUsers(iUser|null $user0, iUser|null $user1): void {
		if(!$user0 || !$user1)
			$this->assertEquals($user0, $user1);
		else {
			$this->assertEquals($user0->getPragmaUserId(), $user1->getPragmaUserId());
			$this->assertEquals($user0->getName(), $user1->getName());
			$this->assertEquals($user0->getSurname(), $user1->getSurname());
			$this->assertEquals($user0->getMiddleName(), $user1->getMiddleName());
			$this->assertEquals($user0->getEmail(), $user1->getEmail());
			$this->assertEquals($user0->getPhone(), $user1->getPhone());
			$this->assertEquals($user0->getLang(), $user1->getLang());
			$this->assertEquals($user0->isConfirmEmail(), $user1->isConfirmEmail());
		}
	}
}