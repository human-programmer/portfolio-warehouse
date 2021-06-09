<?php


namespace Services\Tests;


trait TestPragmaAccounts {
	private static array $testAccounts = [];

	static function createPragmaAccount(string $crmName): int {
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

	static function removeTestAccounts(): void {
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