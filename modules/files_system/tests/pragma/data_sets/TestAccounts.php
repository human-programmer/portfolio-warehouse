<?php


namespace FilesSystem\Pragma\Tests;


use Generals\CRMDB;
use Services\General\Account;
use Services\General\iAccount;

class TestAccounts extends CRMDB {
	private static array $testAccounts = [];

	static function uniqueAccount(): iAccount {
		$model = self::uniqueModel();
		return new Account($model);
	}

	private static function uniqueModel(): array {
		$id = self::createPragmaAccount('amocrm');
		return [
			'pragma_account_id' => $id,
			'pragma_time_create' => time(),
			'crm_name' => 'amocrm',
		];
	}

	static private function createPragmaAccount(string $crmName): int {
		$id = self::createPragmaAccountRow($crmName);
		self::$testAccounts[] = $id;
		return $id;
	}

	private static function createPragmaAccountRow(string $crmName): int {
		$schema = self::getAccountsSchema();
		$crms = self::getCrmNamesSchema();
		$sql = "INSERT INTO $schema ($schema.`crm`, $schema.`test`)
				VALUES((SELECT $crms.`id` FROM $crms WHERE $crms.`name` = '$crmName'), 1)";
		self::executeSql($sql);
		return self::last_id();
	}

	static function clear(): void {
		if(!count(self::$testAccounts)) return;
		$schema = self::getAccountsSchema();
		$condition = self::createRemoveCondition();
		$sql = "DELETE FROM $schema WHERE $condition";
		self::executeSql($sql);
		self::$testAccounts = [];
	}

	private static function createRemoveCondition(): string {
		$schema = self::getAccountsSchema();
		foreach (self::$testAccounts as $index => $id)
			$arr[] = "$schema.`id` = $id";
		return implode(' OR ', $arr);
	}
}