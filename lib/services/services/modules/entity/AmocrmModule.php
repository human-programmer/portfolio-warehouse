<?php


namespace Services\General;


trait AmocrmModule {
	private string $amocrm_integration_id;
	private string $amocrm_code;

	private function amocrmInit(array $model): void {
		$this->amocrm_integration_id = $model['amocrm_integration_id'] ?? '';
		$this->amocrm_code = $model['amocrm_code'] ?? '';
	}

	function setAmocrmIntegreationId(string $id): void {
		// TODO: Implement setAmocrmIntegreationId() method.
	}

	function setAmocrmCode(string $code): void {
		// TODO: Implement setAmocrmCode() method.
	}

	function setAmocrmSecretKey(string $key): void {
		// TODO: Implement setAmocrmSecretKey() method.
	}

	function getAmocrmIntegrationId(): string {
		return $this->amocrm_integration_id;
	}

	function getAmocrmCode(): string {
		return $this->amocrm_code;
	}

	private function getAmocrmModel(): array {
		return [
			'amocrm_integration_id' => $this->getAmocrmIntegrationId(),
			'amocrm_code' => $this->getAmocrmCode(),
		];
	}
}