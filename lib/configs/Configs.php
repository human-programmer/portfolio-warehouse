<?php


namespace Configs;

require_once __DIR__ . '/interface/iConfigs.php';
require_once __DIR__ . '/modules/DbConnect.php';
require_once __DIR__ . '/modules/DbNames.php';
require_once __DIR__ . '/modules/ServiceServer.php';

class Configs implements iConfigs {
	private static bool $ilsLoaded = false;
	private static iDbConnect $dbConnect;
	private static iDbNames $dbNames;
	private static iServiceServer $servicesServer;
	private static bool $isDev;
	private static bool $isHosting;

	static function getDbConnect(): iDbConnect {
		self::checkLoad();
		return self::$dbConnect;
	}

	static function getDbNames(): iDbNames {
		self::checkLoad();
		return self::$dbNames;
	}

	static function getServices(): iServiceServer {
		self::checkLoad();
		return self::$servicesServer;
	}

	private static function checkLoad(): void {
		self::$ilsLoaded || self::load();
	}

	private static function load(): void {
		self::$ilsLoaded = true;
		$model = self::loadConfigModel();
		self::setConfigs($model);
	}

	protected static function setConfigs(array$model){
		self::$dbConnect = new DbConnect($model['DB_CONNECT']);
		self::$dbNames = new DbNames($model['DB_NAMES']);
		self::$servicesServer = new ServiceServer($model['SERVICES_SERVER']);
	}

	protected static function loadConfigModel(): array {
		$fileName = self::getFileName();
		return parse_ini_file($fileName, true);
	}

	private static function getFileName(): string {
		if(self::isDev())
			return self::getDir() . 'dev.ini';
		return self::getDir() . 'deploy.ini';
	}

	static function rootCatalog(): string {
		if(self::isHosting())
			return '/var/www/core_crm/data/';
		return 'C:\\';
	}

	private static function getDir(): string {
		if (self::isHosting())
			return '/var/www/core_crm/data/projects/configs/';
		return 'C:\\node_projects\\configs\\';
	}

	static function isDev(): bool {
		if(isset(self::$isDev)) return self::$isDev;
		self::$isDev = self::isDevPath(__DIR__);
		return self::$isDev;
	}

	static function isDevPath(string $path): bool {
		return !strstr($path, '/smart.core_crm.by/api/');
	}

	static function isHosting(): bool {
		if(isset(self::$isHosting)) return self::$isHosting;
		self::$isHosting = !preg_match('/Windows/i', php_uname());
		return self::$isHosting;
	}

	static function getCurrentDomain(): string {
		return self::isDev() ? 'smart-dev.core_crm.by' : 'smart.core_crm.by';
	}
}