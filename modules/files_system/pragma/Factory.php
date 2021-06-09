<?php


namespace FilesSystem\Pragma;


use Services\General\iNode;

require_once __DIR__ . '/../../../lib/services/Factory.php';
require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../../../lib/db/CRMDB.php';
require_once __DIR__ . '/../CONSTANTS.php';
require_once __DIR__ . '/components/AccountVariables.php';
require_once __DIR__ . '/components/files/Files.php';

class Factory {
	private static iNode $node;
	protected static IFiles $files;
	private static \LogWriter $logger;
	private static IAccountVariables $variables;

	static function initFromParams(string $domain, string $module_code): void {
		self::$logger = new \LogJSON($domain, $module_code, 'FilesSystem');
		\Services\Factory::init($module_code, $domain, self::$logger);
		$node = \Services\Factory::getNodesService()->findAmocrmReferer($module_code, $domain); //TODO: сделать боелее универсальный вариант
		self::$node = $node ?? throw new \Exception("Node not found, domain: '$domain', code: '$module_code'");
	}

	static function init(iNode $node, \LogWriter $logger): void {
		self::$node = $node;
		self::$logger = $logger;
	}

	static function getFiles(): IFiles {
		if(isset(self::$files)) return self::$files;
		self::$files = new Files(self::$node);
		return self::$files;
	}

	static function getAccountVariables(): IAccountVariables {
		if(isset(self::$variables)) return self::$variables;
		self::$variables = new AccountVariables(self::getNode());
		return self::$variables;
	}

	static function getNode(): iNode {
		return self::$node;
	}

	static function getLogWriter(): \LogWriter {
		return self::$logger;
	}

	static function issetLogger(): bool {
		return isset(self::$logger);
	}
}