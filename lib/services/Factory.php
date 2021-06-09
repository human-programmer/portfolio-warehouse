<?php


namespace Services;


use Services\General\iAccountsService;
use Services\General\iDocTemplateService;
use Services\General\iModulesService;
use Services\General\iNodesService;
use Services\General\iUsersService;

require_once __DIR__ . '/../configs/Configs.php';
require_once __DIR__ . '/services/accounts/AccountsService.php';
require_once __DIR__ . '/services/modules/ModulesService.php';
require_once __DIR__ . '/services/nodes/NodesService.php';
require_once __DIR__ . '/services/users/UsersService.php';
require_once __DIR__ . '/services/others/templater/DocTemplateService.php';

class Factory {
	private static $clientModuleCode;
	private static $accountReferer;
	private static \LogWriter $logWriter;

	static function init(string $module_code, string $account_referer, \LogWriter $logWriter): void {
		self::$clientModuleCode = $module_code;
		self::$accountReferer = $account_referer;
		self::$logWriter = $logWriter;
	}

	static function getAccountsService(): iAccountsService {
		return AccountsService::getSelf();
	}

	static function getModulesService(): iModulesService {
		return ModulesService::getSelf();
	}

	static function getNodesService(): iNodesService {
		return NodesService::getSelf();
	}

	static function getUsersService(): iUsersService {
		return UsersService::getSelf();
	}

	static function getTemplateService(): iDocTemplateService {
		return DocTemplateService::getSelf();
	}

	static function getClientModuleCode() {
		return self::$clientModuleCode ?? '';
	}

	static function getAccountReferer() {
		return self::$accountReferer ?? '';
	}

	static function getLogWriter(): \LogWriter {
		return self::$logWriter;
	}

	static function getModuleLifeCycle(): iModuleLifeCycle|null {
		try {
			require_once __DIR__ . '/../../integrations/store/Factory.php';
			return \market\Factory::getModuleLifeCycle();
		} catch (\Exception $exception) {
			return null;
		}
	}
}