<?php


namespace Services\General;


trait PragmaAccount {
	private int $pragma_account_id;
	private int $pragma_time_create;
	private string $crm_name;

	private function pragmaInit(array $model): void {
		$this->pragma_account_id = $model['pragma_account_id'];
		$this->pragma_time_create = $model['pragma_time_create'];
		$this->crm_name = $model['crm_name'];
	}

	function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	function getPragmaTimeCreate(): int {
		return $this->pragma_time_create;
	}

	function getCrmName(): string {
		return $this->crm_name;
	}

	private function getPragmaModel(): array {
		return [
			'pragma_account_id' => $this->getPragmaAccountId(),
			'pragma_time_create' => $this->getPragmaTimeCreate(),
			'crm_name' => $this->getCrmName(),
		];
	}
}