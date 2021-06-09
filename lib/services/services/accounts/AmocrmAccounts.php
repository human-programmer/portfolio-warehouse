<?php


namespace Services;


use Services\General\iAccount;

trait AmocrmAccounts {

	function findAmocrmById(int $amocrm_account_id): iAccount|null {
		$query = self::createFindAmocrmByIdQuery($amocrm_account_id);
		return $this->singleGetMethod($query);
	}

	private static function createFindAmocrmByIdQuery(int|array $amocrm_account_id): array {
		$filter = ['amocrm_account_id' => $amocrm_account_id];
		return self::createQueryWithFilter($filter);
	}

	function findAmocrmBySubdomain(string $subdomain): iAccount|null {
		$referer = "$subdomain.amocrm.ru";
		return $this->findAmocrmByReferer($referer);
	}

	function findAmocrmByReferer(string $referer): iAccount|null {
		$query = self::createFindAmocrmByReferer($referer);
		return $this->singleGetMethod($query);
	}

	private static function createFindAmocrmByReferer(string|array $amocrm_referer): array {
		$filter = ['amocrm_referer' => $amocrm_referer];
		return self::createQueryWithFilter($filter);
	}

	function findBitrix24ByMemberId(string $member_id): iAccount|null {
//		throw new \Exception("Method findBitrix24ByMemberId is not implemented");
		return null;
	}

	private function singleGetMethod(array $query): iAccount|null {
		$answer = $this->amocrmGetMethod($query);
		return $answer[0] ?? null;
	}

	private function amocrmGetMethod(array $query): array {
		$answer = $this->amocrmRequest('get', $query);
		return self::createStructs($answer['result']);
	}

	private function amocrmRequest(string $method, array $query): array {
		$path = "/amocrm/accounts/$method";
		return $this->servicesRequest($path, $query);
	}
}