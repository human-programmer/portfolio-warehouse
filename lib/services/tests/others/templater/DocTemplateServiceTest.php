<?php


namespace Services\Tests;


use Services\DocTemplateService;
use Services\Factory;

require_once __DIR__ . '/../../TestFactory.php';
require_once __DIR__ . '/TestDataSets.php';

class DocTemplateServiceTest extends \PHPUnit\Framework\TestCase {
	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
		\Files\Tests\TestFactory::testInit();
//		$logger = new \LogJSON('pragmadev.amocrm.ru', 'TemplateEngine', '');
//		Factory::init('TemplateEngine', 'pragmadev.amocrm.ru', $logger);
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTests();
		\Files\Tests\TestFactory::clearDataSets();
	}

	function testAmoCreateFromEntities(){
		$template = TestDataSets::getUniqueTemplateLink();
		$params = TestDataSets::getParams();
		$result = DocTemplateService::getSelf()->amoCreateFromEntities($template, $params);
		$this->assertTrue(!!$result);
	}
}