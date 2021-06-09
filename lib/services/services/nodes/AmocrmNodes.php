<?php


namespace Services;


use Services\General\iNode;

trait AmocrmNodes {
	function findAmocrmNode(string $amocrm_client_id, string $amocrm_subdomain): iNode|null {
		$query = self::createFindAmocrmNodeFilter($amocrm_client_id, $amocrm_subdomain);
		return $this->amocrmGetMethod($query)[0] ?? null;
	}

	private static function createFindAmocrmNodeFilter(string|array $amocrm_client_id, string|array $amocrm_subdomain): array {
		$referer = $amocrm_subdomain . '.amocrm.ru';
		$filter = ['amocrm_integration_id' => $amocrm_client_id, 'amocrm_referer' => $referer];
		return self::createQueryWithFilter($filter);
	}

	function findAmocrmReferer(string $pragma_module_code, string $referer): iNode|null {
		$amocrm_subdomain = self::fetchSubdomain($referer);
		return $this->findAmocrmNodeCode($pragma_module_code, $amocrm_subdomain);
	}

	private static function fetchSubdomain(string $referer): string {
		$arr = explode('.', $referer);
		return $arr[0];
	}

	function findAmocrmNodeCode(string $pragma_module_code, string $amocrm_subdomain): iNode|null {
		$query = self::createFindAmocrmNodeCodeFilter($pragma_module_code, $amocrm_subdomain);
		return $this->amocrmGetMethod($query)[0] ?? null;
	}

	private static function createFindAmocrmNodeCodeFilter(string|array $pragma_module_code, string|array $amocrm_subdomain): array {
		$referer = $amocrm_subdomain . '.amocrm.ru';
		$filter = ['code' => $pragma_module_code, 'amocrm_referer' => $referer];
		return self::createQueryWithFilter($filter);
	}

	function findAmocrmNodeAccId(string $pragma_module_code, int $amocrm_account_id): iNode|null {
		$query = self::createFindAmocrmNodeAccIdFilter($pragma_module_code, $amocrm_account_id);
		return $this->amocrmGetMethod($query)[0] ?? null;
	}

	private static function createFindAmocrmNodeAccIdFilter(string|array $pragma_module_code, int|array $amocrm_account_id): array {
		$filter = ['code' => $pragma_module_code, 'amocrm_account_id' => $amocrm_account_id];
		return self::createQueryWithFilter($filter);
	}

	private function amocrmGetMethod(array $query): array {
		$answer = $this->amocrmRequest('get', $query);
		return self::createStructs($answer['result']);
	}

	function amocrmInstall(string $client_id, string $referer, string $code): iNode {
		$query = self::createAmocrmInstallQuery($client_id, $referer, $code);
		$nodes = $this->amocrmRequest('install', $query)['result'];
		$node = self::createStructs($nodes)[0];
		self::installTrigger($node);
		return $node;
	}

	private static function installTrigger(iNode $node): void {
		try {
			Factory::getModuleLifeCycle()?->installEvent($node);
		} catch (\Exception $e) {
			Factory::getLogWriter()->send_error($e);
		}
	}

	private static function createAmocrmInstallQuery(string $client_id, string $referer, string $code): array {
		return [
			'code' => $code,
			'referer' => $referer,
			'client_id' => $client_id,
			'from_widget' => '',
		];
	}

	function amocrmUpdateQuery(array $query): iNode {
		$nodes = $this->amocrmRequest('update', $query)['result'];
		return self::createStructs($nodes)[0];
	}

	function amocrmRestQuery(array $query): mixed {
		return $this->amocrmRequest('rest.gateway', $query);
	}

	private function amocrmRequest(string $method, array $query): mixed {
		$path = "/amocrm/nodes/$method";
		return $this->servicesRequest($path, $query);
	}
}