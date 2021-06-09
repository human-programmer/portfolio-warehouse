<?php


namespace Services\Tests;


use Generals\Functions\Date;
use Services\General\Account;
use Services\General\iAccount;


require_once __DIR__ . '/TestPragmaAccounts.php';
require_once __DIR__ . '/TestAmocrmAccounts.php';

class TestAccounts extends \Generals\CRMDB {
	use TestPragmaAccounts, TestAmocrmAccounts;

	static function createUniqueAccounts(): array {
		return [
			self::createUniqueAccount(),
			self::createUniqueAccount(),
			self::createUniqueAccount(),
			self::createUniqueAccount(),
		];
	}

	static function createUniqueAccount(string $crmName = 'amocrm'): iAccount {
		$id = self::createPragmaAccount($crmName);
		self::createAmocrmInterface($id);
		return self::getAccount($id);
	}

	static function createTargetAmocrmAccount(string $subdomain, int $amocrm_id): iAccount {
		$id = self::createPragmaAccountRow('amocrm');
		self::createAmocrmInterface($id, $subdomain, $amocrm_id);
		return self::getAccount($id);
	}

	private static function getAccount(int $id): iAccount {
		$model = self::getAccountModel($id);
		$model = self::formattingModel($model);
		return new Account($model);
	}

	private static function formattingModel(array $model): array {
		$model['amocrm_referer'] = $model['amocrm_subdomain'] . '.amocrm.ru';
		$model['pragma_time_create'] = Date::getIntTimeStamp($model['pragma_date_create']);
		return $model;
	}

	private static function getAccountModel(int $id): array {
		$pragma = self::getAccountsSchema();
		$condition = "$pragma.id = $id";
		$sql = self::sql($condition);
		return self::querySql($sql)[0];
	}

	private static function sql(string $condition): string {
		$pragma = self::getAccountsSchema();
		$crmNames = self::getCrmNamesSchema();
		$amocrm = self::getAmocrmAccountsSchema();
		return "SELECT
					$pragma.`id` AS `pragma_account_id`,
					$pragma.`date_create` AS `pragma_date_create`,
					$crmNames.`name` AS `crm_name`,
       
					$amocrm.`id` AS `amocrm_account_id`,
					$amocrm.`subdomain` AS `amocrm_subdomain`,
					$amocrm.`country` AS `amocrm_country`,
					$amocrm.`created_at` AS `amocrm_created_at`,
					$amocrm.`created_by` AS `amocrm_created_by`,
					$amocrm.`is_technical_account` AS `amocrm_is_technical`,
					$amocrm.`name` AS `amocrm_name`
				FROM $pragma
					INNER JOIN $crmNames ON $crmNames.id = $pragma.crm
					INNER JOIN $amocrm ON $amocrm.pragma_id = $pragma.id
				WHERE $condition";
	}
}