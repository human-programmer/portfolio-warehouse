<?php


namespace Services\Tests;

use Services\General\iUser;
use Services\General\iUsersService;
use Services\General\iUserToCreate;
use Services\UsersService;

require_once __DIR__ . '/../TestFactory.php';


class UsersServiceTest extends \PHPUnit\Framework\TestCase {
	private static array $usersIdToDelete = [];
	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	private static function removeTestUsers(): void {
		TestUsers::removeTestUsers(self::$usersIdToDelete);
		self::$usersIdToDelete = [];
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTests();
		self::removeTestUsers();
	}

	function testGetSelf(){
		$users = UsersService::getSelf();
		$this->assertInstanceOf(iUsersService::class, $users);
	}

	function testFindByPhone(){
		$testUsers = TestUsers::createUniqueUsers();
		$answer = TestFactory::getUsersService()->findByPhone($testUsers[0]->getPhone());
		$this->compareUsers($testUsers[0], $answer);
	}

	function testFindByEmail(){
		$testUsers = TestUsers::createUniqueUsers();
		$answer = TestFactory::getUsersService()->findByEmail($testUsers[0]->getEmail());
		$this->compareUsers($testUsers[0], $answer);
	}

	function testFindByPragmaId(){
		$testUsers = TestUsers::createUniqueUsers();
		$answer = TestFactory::getUsersService()->findByPragmaId($testUsers[0]->getPragmaUserId());
		$this->compareUsers($testUsers[0], $answer);
	}

	function testCreateUser(){
		$userToCreate = TestCreateUser::createSelfUnique();
		$user = UsersService::getSelf()->createUser($userToCreate);
		$this->compareUsersCreateStruct($user, $userToCreate);
		self::$usersIdToDelete[] = $user->getPragmaUserId();
	}

	private function compareUsers(iUser $user0, iUser $user1): void {
		$this->compareUsersCreateStruct($user0, $user1);
		$this->assertEquals($user0->getPragmaUserId(), $user1->getPragmaUserId());
		$this->assertEquals($user0->isConfirmEmail(), $user1->isConfirmEmail());
	}

	private function compareUsersCreateStruct(iUserToCreate $user0, iUserToCreate $user1): void {
		$this->assertEquals($user0->getName(), $user1->getName());
		$this->assertEquals($user0->getSurname(), $user1->getSurname());
		$this->assertEquals($user0->getMiddleName(), $user1->getMiddleName());
		$this->assertEquals($user0->getEmail(), $user1->getEmail());
		$this->assertEquals($user0->getPhone(), $user1->getPhone());
		$this->assertEquals($user0->getLang(), $user1->getLang());
	}
}