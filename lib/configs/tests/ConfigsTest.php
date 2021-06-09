<?php


namespace Configs\Tests;


use Configs\Configs;
use Configs\iDbConnect;
use Configs\iDbNames;
use Configs\iServiceServer;

require_once __DIR__ . '/../Configs.php';
require_once __DIR__ . '/TestConfigs.php';

class ConfigsTest extends \PHPUnit\Framework\TestCase {
	function testIsDevPath(){
		$devPath = 'www/smart-dev.core_crm.by/api/lib';
		$deployPath = 'www/smart.core_crm.by/api/lib';
		$this->assertTrue(Configs::isDevPath($devPath));
		$this->assertFalse(Configs::isDevPath($deployPath));
	}

	function testIsHosting(){
		$this->assertFalse(Configs::isHosting());
	}

	function testGetDbConnect(){
		$this->assertInstanceOf(iDbConnect::class, Configs::getDbConnect());
		$this->checkTestDbConnect();
	}

	function testGetDbNames(){
		$this->assertInstanceOf(iDbNames::class, Configs::getDbNames());
		$this->checkTestDbNames();
	}

	function testGetServices(){
		$this->assertInstanceOf(iServiceServer::class, Configs::getServices());
		$this->checkTestServices();
	}

	private function checkTestDbConnect(): void {
		$connect = TestConfigs::getDbConnect();
		$model = TestConfigs::getTestConfigModel()['DB_CONNECT'];
		$this->assertEquals($model['host'], $connect->getHost());
		$this->assertEquals($model['dbname'], $connect->getDbName());
		$this->assertEquals($model['user'], $connect->getUser());
		$this->assertEquals($model['password'], $connect->getPassword());
	}

	private function checkTestDbNames(): void {
		$model = TestConfigs::getTestConfigModel()['DB_NAMES'];
		$dbNames = TestConfigs::getDbNames();
		$this->assertEquals($model['amocrm_interface'], $dbNames->getAmocrmInterface());
		$this->assertEquals($model['bitrix24_interface'], $dbNames->getBitrix24Interface());
		$this->assertEquals($model['dashboard'], $dbNames->getDashboard());
		$this->assertEquals($model['calculator'], $dbNames->getCalculator());
		$this->assertEquals($model['pragmacrm'], $dbNames->getCoreCrm());
		$this->assertEquals($model['modules'], $dbNames->getModules());
		$this->assertEquals($model['users'], $dbNames->getUsers());
		$this->assertEquals($model['storage'], $dbNames->getStorage());
		$this->assertEquals($model['additional_storage'], $dbNames->getAdditionalStorage());
		$this->assertEquals($model['market'], $dbNames->getMarket());
	}

	private function checkTestServices(): void {
		$model = TestConfigs::getTestConfigModel()['SERVICES_SERVER'];
		$this->assertEquals($model['port'], TestConfigs::getServices()->getPort());
		$this->assertEquals($model['host'], TestConfigs::getServices()->getHost());
	}
}