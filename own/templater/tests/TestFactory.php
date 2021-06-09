<?php


namespace Templater\Tests;


use Services\Tests\TestNodes;
use Templater\Factory;

require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/TestDataSets.php';
require_once __DIR__ . '/../../../lib/services/tests/TestFactory.php';
require_once __DIR__ . '/../../../modules/files/tests/TestFactory.php';

class TestFactory extends Factory {

	static function initTest(): void{
		parent::init('testCore');
		self::initNode();
	}

	protected static function initNode(): void {
		self::$node = TestNodes::createUniqueNode();
		\Files\Tests\TestFactory::init(self::getNode(), self::getLogWriter());
		\Files\Tests\TestFactory::resetTestFiles();
	}

	static function clearTest(): void {
		TestNodes::removeTestEntities();
		TestDataSets::clearTests();
	}
}