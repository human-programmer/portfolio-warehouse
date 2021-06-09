<?php


namespace Services;


trait AmocrmNode {
	private bool $amocrm_enable;

	private function amocrmInit(array $model): void {
		$this->amocrm_enable = !!$model['amocrm_enable'];
	}

	function isAmocrmEnable(): bool {
		return $this->amocrm_enable;
	}

	function amocrmInstall(string $clientId, string $referer, string $code): void {
		// TODO: Implement amocrmInstall() method.
	}

	function amocrmRestQuery(string $route, string $method, $params = null, array $headers = []): mixed {
		$query = self::createRESTQuery($route, $method, $params, $headers);
		return NodesService::getSelf()->amocrmRestQuery($query)['result'];
	}

	private function createRESTQuery(string $route, string $method, $params = null, array $headers = []): array {
		$default = Service::createDefaultQuery($this->getModule()->getCode(), $this->getAccount()->getAmocrmReferer());
		$default['data'] = [
			'uri' => $route,
			'method' => $method,
			'body' => $params,
			'headers' => $headers,
		];
		return $default;
	}

	function setAmocrmDisable(): void {
		$query = $this->createAmocrmDisableQuery();
		$newNode = NodesService::getSelf()->amocrmUpdateQuery($query);
		$this->amocrm_enable = $newNode->isAmocrmEnable();
	}

	private function createAmocrmDisableQuery(): array {
		$defaultQuery = $this->createDefaultQuery();
		$query['data'] = [
			'pragma_account_id' => $this->getAccount()->getPragmaAccountId(),
			'pragma_module_id' => $this->getModule()->getPragmaModuleId(),
			'amocrm_disabled' => true
		];
		return array_merge($defaultQuery, $query);
	}

	private function getAmocrmModel(): array {
		return [
			'amocrm_enable' => $this->isAmocrmEnable()
		];
	}
}