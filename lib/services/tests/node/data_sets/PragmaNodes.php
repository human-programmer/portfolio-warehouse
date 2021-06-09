<?php


namespace Services\Tests;


use Generals\Functions\Date;
use Services\General\iAccount;
use Services\General\iModule;
use Services\General\iUser;

trait PragmaNodes {

	private static function createPragmaNodeInDb(iModule $module, iAccount $account, iUser|null $user = null): void {
		$pragma = self::getPragmaAccountsModuleSchema();
		$sql = "INSERT INTO $pragma (module_id, account_id, user_id, enable_date)
				VALUES (:module_id, :account_id, :user_id, :enable_date)";
		$model = ['module_id' => $module->getPragmaModuleId(),
				'account_id' => $account->getPragmaAccountId(),
				'user_id' => $user?->getPragmaUserId(),
				'enable_date' => self::getShutdownDate($module)];
		self::executeSql($sql, $model);
	}

	private static function getShutdownDate(iModule $module): string {
		$timestamp = time() + 86400 * $module->getFreePeriodDays();
		return Date::getStringTimeStamp($timestamp);
	}

	private static function getPragmaModel(iModule $module, iAccount $account): array {
		$module_id = $module->getPragmaModuleId();
		$account_id = $account->getPragmaAccountId();
		$model = self::loadPragmaModel($module_id, $account_id);
		return self::formattingPragmaModel($model);
	}

	private static function formattingPragmaModel(array $model): array {
		$model['shutdown_time'] = Date::getIntTimeStamp($model['pragma_enable_date']);
		$model['is_pragma_active'] = time() < $model['shutdown_time'];
		$model['is_once_installed'] = true;
		return $model;
	}

	private static function loadPragmaModel(int $module_id, int $account_id): array {
		$pragma = self::getPragmaAccountsModuleSchema();
		$sql = "SELECT
					$pragma.user_id AS pragma_user_id,
       				$pragma.enable_date AS pragma_enable_date,
       				$pragma.unlimited_time AS is_unlimited
				FROM $pragma 
				WHERE module_id = $module_id AND  account_id = $account_id";
		return self::querySql($sql)[0];
	}
}