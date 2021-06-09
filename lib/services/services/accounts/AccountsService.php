<?php


namespace Services;


use Services\General\Account;
use Services\General\iAccount;
use Services\General\iAccountsService;


require_once __DIR__ . '/../../business_rules/general/account/iAccountsService.php';
require_once __DIR__ . '/../Service.php';
require_once __DIR__ . '/../../services/accounts/entity/Account.php';
require_once __DIR__ . '/PragmaAccounts.php';
require_once __DIR__ . '/AmocrmAccounts.php';

class AccountsService extends Service implements iAccountsService {
	use AmocrmAccounts, PragmaAccounts;
	private static self $inst;

	static function getSelf(): AccountsService {
		if(isset(self::$inst))
			return self::$inst;
		self::$inst = new self();
		return self::$inst;
	}

	private static function createStructs(array $models): array {
		foreach ($models as $model)
			$result[] = self::createStruct($model);
		return $result ?? [];
	}

	private static function createStruct(array $model): iAccount {
		return new Account($model);
	}
}