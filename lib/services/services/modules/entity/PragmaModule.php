<?php


namespace Services\General;


trait PragmaModule {
	private int $pragma_module_id;
	private string $code;
	private int $free_period_days;
	private bool $is_free;

	private function pragmaInit(array $model): void {
		$this->pragma_module_id = $model['pragma_module_id'];
		$this->code = $model['code'];
		$this->free_period_days = $model['free_period_days'];
		$this->is_free = $model['is_free'] ?? false;
	}

	function getPragmaModuleId(): int {
		return $this->pragma_module_id;
	}

	function getFreePeriodDays(): int {
		return $this->free_period_days;
	}

	function setFreePeriodDays(int $days): void {
		// TODO: Implement setFreePeriodDays() method.
	}

	function getCode(): string {
		return $this->code;
	}

	function setCode(string $code): void {
		// TODO: Implement setCode() method.
	}

	function setFree(): void {
		// TODO: Implement setFree() method.
	}

	function isFree(): bool {
		return $this->is_free;
	}

	private function getPragmaModel(): array {
		return [
			'pragma_module_id' => $this->getPragmaModuleId(),
			'code' => $this->getCode(),
			'free_period_days' => $this->getFreePeriodDays(),
			'is_free' => $this->isFree(),
		];
	}
}