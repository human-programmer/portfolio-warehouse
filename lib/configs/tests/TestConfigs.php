<?php


namespace Configs\Tests;


require_once __DIR__ . '/../Configs.php';

class TestConfigs extends \Configs\Configs {
	protected static function loadConfigModel(): array {
		return self::getTestConfigModel();
	}

	static function getTestConfigModel(): array {
		return [
			'DB_CONNECT'=> [
				'host' => '127.0.0.1',
				'dbname' => 'pragma_crm_test',
				'user' => 'root',
				'password' => 'root',
			],
			'DB_NAMES' => [
				'amocrm_interface'=> 'amocrm_interface_test',
				'bitrix24_interface' => 'bitrix_interface_dev',
				'dashboard' => 'pragma_crm',
				'calculator' => 'pragma_calculator',
				'pragmacrm' => 'pragma_crm_test',
				'modules' => 'pragma_modules',
				'users' => 'pragma_users',
				'storage' => 'pragma_storage',
				'additional_storage' => 'pragma_storage_additional_dev',
				'market' => 'pragma_market_dev',
			],
			'SERVICES_SERVER'=> [
				'port' => 19000,
				'host' => '127.0.0.1',
			],
		];
	}
}