<?php


namespace Services;


trait PragmaNode {
	private int $shutdown_time;
	private bool $is_unlimited;
	private bool $is_once_installed;
	private bool $is_pragma_active;

	private function pragmaInit(array $model): void {
		$this->shutdown_time = $model['shutdown_time'];
		$this->is_unlimited = $model['is_unlimited'];
		$this->is_once_installed = $model['is_once_installed'];
		$this->is_pragma_active = $model['is_pragma_active'];
	}

	function isOnceInstalled(): bool {
		return $this->is_once_installed;
	}

	function getShutdownTime(): int {
		return $this->shutdown_time;
	}

	function isUnlimited(): bool {
		return $this->is_unlimited;
	}

	function isPragmaActive(): bool {
		return $this->is_pragma_active;
	}

	function checkActive(): void {
		if(!$this->isActive())
			throw new \Exception("Not paid", 667);
	}

	function createInactiveApiKey(int $pragma_user_id): string {
		$defaultQuery = $this->createDefaultQuery();
		$query = ['pragma_account_id' => $this->getAccount()->getPragmaAccountId(), 'pragma_user_id' => $pragma_user_id];
		$query = array_merge($defaultQuery, $query);
		return NodesService::getSelf()->createInactiveApiKey($query);
	}

	function checkApiKey(string $token): bool {
		$defaultQuery = $this->createDefaultQuery();
		$query = ['pragma_account_id' => $this->getAccount()->getPragmaAccountId(), 'api_key' => $token];
		$query = array_merge($defaultQuery, $query);
		return NodesService::getSelf()->checkApiKey($query);
	}

	function setShutdownTime(int $time): void {
		$query = $this->createShutdownTimeQuery($time);
		$newNode = NodesService::getSelf()->amocrmUpdateQuery($query);
		$this->shutdown_time = $newNode->getShutdownTime();
	}

	private function createShutdownTimeQuery(int $time): array {
		$defaultQuery = $this->createDefaultQuery();
		$query['data'] = [
			'pragma_account_id' => $this->getAccount()->getPragmaAccountId(),
			'pragma_module_id' => $this->getModule()->getPragmaModuleId(),
			'shutdown_time' => $time
		];
		return array_merge($defaultQuery, $query);
	}

	private function createDefaultQuery(): array {
		return NodesService::createDefaultQuery($this->getModule()->getCode(), $this->getAccount()->getDomain());
	}

	private function getPragmaModel(): array {
		return [
			'shutdown_time' => $this->getShutdownTime(),
			'is_unlimited' => $this->isUnlimited(),
			'is_once_installed' => $this->isOnceInstalled(),
			'is_pragma_active' => $this->isPragmaActive(),
		];
	}
}