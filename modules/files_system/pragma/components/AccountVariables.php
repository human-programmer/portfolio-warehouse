<?php


namespace FilesSystem\Pragma;


use Configs\Configs;
use Services\General\iNode;

require_once __DIR__ . '/../business_rules/IAccountVariables.php';

class AccountVariables implements IAccountVariables {
	private static $rootDir = '/var/www/core_crm/data';

	function __construct(private iNode $node) {}

	function getNodeDir(): string {
		$account_id = $this->node->getAccount()->getPragmaAccountId();
		$module_id = $this->node->getModule()->getPragmaModuleId();
		return self::dir($account_id, $module_id);
	}

	static function rootDirByFile(IFile $file): string {
		return self::dir($file->getAccountId(), $file->getModuleId());
	}

	private static function dir(int $account_id, int $module_id): string {
		return self::getRootDirectory() . "/$account_id/$module_id";
	}

	private static function getRootDirectory(): string {
		$dir = Configs::isDev() ? 'indexed_files_dev' : 'indexed_files';
		return self::$rootDir . "/$dir";
	}

	static function getDefaultExternalPath(): string {
		return static::getStartDomain() . '/api/modules/files_system/core_crm/download.php';
	}

	protected static function getStartDomain(): string {
		return 'https://' . Configs::getCurrentDomain();
	}

	static function setRootDorForTest(string $dir): void {
		self::$rootDir = $dir;
	}
}