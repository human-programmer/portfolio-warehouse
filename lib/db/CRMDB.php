<?php


namespace Generals;


use Configs\Configs;
use Generals\Functions\Date;

require_once __DIR__ . '/../configs/Configs.php';
require_once __DIR__ . '/../generals/functions/Date.php';
require_once __DIR__ . '/modules/AmocrmInterfaceDB.php';
require_once __DIR__ . '/modules/ModulesDB.php';
require_once __DIR__ . '/modules/CoreCRMDB.php';
require_once __DIR__ . '/modules/UsersDB.php';
require_once __DIR__ . '/modules/CalculatorDB.php';
require_once __DIR__ . '/modules/Dashboard.php';
require_once __DIR__ . '/modules/StorageAdditionalDB.php';
require_once __DIR__ . '/modules/StorageDB.php';
require_once __DIR__ . '/modules/Bitrix24InterfaceDB.php';
require_once __DIR__ . '/modules/MarketDB.php';
require_once __DIR__ . '/modules/FilesDB.php';
require_once __DIR__ . '/modules/TemplaterDB.php';
require_once __DIR__ . '/../generals/functions/Formatting.php';

class CRMDB {
	use AmocrmInterfaceDB,
		Bitrix24InterfaceDB,
		ModulesDB,
		CoreCRMDB,
		UsersDB,
		CalculatorDB,
		StorageDB,
        MarketDB,
		Dashboard,
		FilesDB,
		TemplaterDB,
        StorageAdditionalDB;

	static private string $charset = 'utf8';
	static private \PDO $pdo;

	protected function __construct() {
		self::init();
	}

	static private function init(): void {
		if (!isset(self::$pdo))
			self::initDBConnection();
	}

	static protected function initDBConnection() : void {
		$config = Configs::getDbConnect();
		$dsn = 'mysql:host=' . $config->getHost() . ';dbname=' . $config->getDbName() . ';charset=' . self::$charset;
		self::$pdo = new \PDO($dsn, $config->getUser(), $config->getPassword(), self::getOptions());
	}

	static private function getOptions(): array {
		return [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, \PDO::ATTR_EMULATE_PREPARES => false,];
	}

	static protected function query(string $request){
		self::init();
		$stmt = self::$pdo->query($request);
		return $stmt ? $stmt->fetchAll() : false;
	}

	static protected function querySql(string $request) : array {
		$result = self::query($request);
		if($request === false)
			throw new \Exception('Unknown Error');
		return $result;
	}

	static protected function execute(string $request, array $params = []) {
		self::init();
		$stmt = self::$pdo->prepare($request);
		return $stmt->execute($params);
	}

	static protected function executeSql(string $request, array $params = []) : void {
		if(self::execute($request, $params) === false)
			throw new \Exception('Unknown Error');
	}

	static protected function last_id() {
		return self::querySql('SELECT @@IDENTITY AS id')[0]['id'] ?? null;
	}

	//2020-10-22 10:42:46
	static protected function convertToTimestamp(string $date_time, string $format = null): int {
		return $format ? Date::getIntTimeStamp($date_time, $format) : Date::getIntTimeStamp($date_time);
	}

	static protected function getCurrentTimeStamp(): string {
		return self::getFormattedTimeStamp(time());
	}

	static protected function getFormattedTimeStamp(int $time): string {
		return Date::getStringTimeStamp($time);
	}

	static protected function escape(string $str): string {
		return self::$pdo->quote($str);
	}
}