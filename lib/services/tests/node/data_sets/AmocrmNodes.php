<?php


namespace Services\Tests;


use Services\General\iAccount;
use Services\General\iModule;

trait AmocrmNodes {


	private static function createAmocrmNodeInDb(iModule $module, iAccount $account): void {
		$amocrm = self::getAmocrmModuleTokensSchema();
		$sql = "INSERT INTO $amocrm (module_id, pragma_account_id, `enable`, access_token, refresh_token, date_time)
				VALUES (:module_id, :account_id, :enable, :access_token, :refresh_token, :date_time)";
		$model = [
			'module_id' => $module->getPragmaModuleId(),
			'account_id' => $account->getPragmaAccountId(),
			'enable' => 1,
			'access_token' => uniqid('access'),
			'refresh_token' => uniqid('refresh'),
			'date_time' => time() + 86400,
		];
		self::executeSql($sql, $model);
	}

	private static function getAmocrmModel(int $module_id, int $account_id): array {
		$amocrm = self::getAmocrmModuleTokensSchema();
		$condition = "$amocrm.module_id = $module_id AND $amocrm.pragma_account_id = $account_id";
		$sql = self::sql($condition);
		return self::querySql($sql)[0];
	}

	private static function sql(string $condition): string {
		$amocrm = self::getAmocrmModuleTokensSchema();
		return "SELECT
					$amocrm.enable AS `amocrm_enable`
				FROM $amocrm 
				WHERE $condition";
	}
}