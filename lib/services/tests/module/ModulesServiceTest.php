<?php


namespace Services\Tests;


use Services\ModulesService;

require_once __DIR__ . '/../TestFactory.php';

class ModulesServiceTest extends \PHPUnit\Framework\TestCase {
	function testGetSelf(){
		$service = ModulesService::getSelf();
		$this->assertInstanceOf(ModulesService::class, $service);
	}
}