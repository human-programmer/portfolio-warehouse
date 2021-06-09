<?php


namespace Services\Tests;


trait TestAmocrmAccounts {
	private static function createAmocrmInterface(int $pragma_account_id, string $subdomain = null, int $amocrm_id = null): void {
		$amocrmAccount = self::getAmocrmAccountsSchema();
		$sql = "INSERT INTO $amocrmAccount (id, pragma_id, subdomain, name, created_at, created_by, country, is_technical_account)
				VALUES(:id, :pragma_id, :subdomain, :name, :created_at, :created_by, :country, :is_technical_account)";
		$model = [
			'id' => $amocrm_id ?? rand(1, 9999999),
			'pragma_id' => $pragma_account_id,
			'subdomain' => $subdomain ?? uniqid('test'),
			'name' => uniqid('test'),
			'created_at' => rand(1, 9999999),
			'created_by' => rand(1, 9999999),
			'country' => 'BY',
			'is_technical_account' => rand(0, 1),
		];
		self::executeSql($sql, $model);
	}
}