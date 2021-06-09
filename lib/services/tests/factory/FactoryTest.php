<?php


namespace Services\Tests;


use Services\AccountsService;
use Services\Factory;
use Services\ModulesService;
use Services\NodesService;
use Services\UsersService;

require_once __DIR__ . '/../../Factory.php';

class FactoryTest extends \PHPUnit\Framework\TestCase {
	function testGetAccountsService(){
		$service = Factory::getAccountsService();
		$this->assertInstanceOf(AccountsService::class, $service);
	}

	function testGetModulesService(){
		$service = Factory::getModulesService();
		$this->assertInstanceOf(ModulesService::class, $service);
	}

	function testGetNodesService(){
		$service = Factory::getNodesService();
		$this->assertInstanceOf(NodesService::class, $service);
	}

	function testGetUsersService(){
		$service = Factory::getUsersService();
		$this->assertInstanceOf(UsersService::class, $service);
	}
}