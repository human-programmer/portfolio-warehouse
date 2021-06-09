<?php


namespace Services;


use Services\General\iAccount;

trait PragmaAccounts {
	function getAccount(int $pragma_account_id): iAccount {
		$query = self::createGetPragmaAccountsQuery($pragma_account_id);
		$answer = $this->pragmaGetMethod($query);
		if(!count($answer))
			throw new \Exception("Account not found: $pragma_account_id");
		return $answer[0];
	}
	private static function createGetPragmaAccountsQuery(int|array $pragma_account_id): array {
		$filter = ['pragma_account_id' => $pragma_account_id];
		return self::createQueryWithFilter($filter);
	}

	private function pragmaGetMethod(array $query): array {
		$answer = $this->pragmaRequest('get', $query);
		return self::createStructs($answer['result']);
	}

	private function pragmaRequest(string $method, array $query): array {
		$path = "/core_crm/accounts/$method";
		return $this->servicesRequest($path, $query);
	}
}