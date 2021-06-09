<?php


namespace Services\Tests;


use Services\Factory;
use Services\General\iAccount;
use Services\General\iNode;

require_once __DIR__ . '/../../db/CRMDB.php';
require_once __DIR__ . '/account/data_sets/TestAccounts.php';
require_once __DIR__ . '/module/data_sets/TestModules.php';
require_once __DIR__ . '/node/data_sets/TestNodes.php';
require_once __DIR__ . '/user/data_sets/TestUsers.php';

require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/../../log/LogJSON.php';

class TestFactory extends Factory {
	static iAccount $account;
	static function initTest(): void {
		self::$account = TestAccounts::createUniqueAccount();
		$module = TestModules::createUniqueModule();
		$logWriter = new \LogJSON(self::$account->getDomain(), $module->getCode(), 'testFactory');
		self::init($module->getCode(), self::$account->getDomain(), $logWriter);
	}

	static function clearTests(): void {
		TestAccounts::removeTestAccounts();
		TestModules::removeTestModules();
		TestNodes::removeTestEntities();
		TestUsers::removeTestUsers();
	}

	public static function getTestAccount(): iAccount {
		return self::$account;
	}

	static function uniqueNode(): iNode {
		return TestNodes::createUniqueNode();
	}
}