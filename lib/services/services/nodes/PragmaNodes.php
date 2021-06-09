<?php


namespace Services;


use Services\General\iNode;

trait PragmaNodes {
	function findPragmaNode(string $module_code, int $pragma_account_id): iNode|null {
		$query = self::createFindPragmaNodeQuery($module_code, $pragma_account_id);
		return $this->pragmaGetMethod($query)[0] ?? null;
	}

	private static function createFindPragmaNodeQuery(string $pragma_code, int $pragma_account_id): array {
		$filter = [
			'pragma_account_id' => $pragma_account_id,
			'code' => $pragma_code
		];
		return self::createQueryWithFilter($filter);
	}

	function createInactiveApiKey(array $query): string {
		$result = $this->pragmaPostRequest('create.inactive.api.key', $query);
		return $result['api_key'];
	}

	function checkApiKey(array $query): bool {
		$result = $this->pragmaPostRequest('check.api.key', $query);
		return $result['status'] === 'success';
	}

	private function pragmaGetMethod(array$query): array {
		$result = $this->pragmaPostRequest('get', $query);
		return self::createStructs($result);
	}

	private function pragmaPostRequest(string $nodeMethod, array $query): array {
		$route = self::getPragmaRoute($nodeMethod);
		return $this->servicesRequest($route, $query)['result'];
	}

	private static function getPragmaRoute(string $method): string {
		return "/core_crm/nodes/$method";
	}
}