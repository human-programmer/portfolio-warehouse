<?php


namespace Services\General;


trait Bitrix24Module {
	private string $bitrix24_integration_id;
	private string $bitrix24_handler_path;

	private function bitrix24Init(array$model): void {
		$this->bitrix24_integration_id = $model['bitrix24_integration_id'] ?? '';
		$this->bitrix24_handler_path = $model['bitrix24_handler_path'] ?? '';
	}

	function getBitrix24IntegrationId(): string {
		return $this->bitrix24_integration_id;
	}

	function setBitrix24SecretKey(string $key): void {
		// TODO: Implement setBitrix24SecretKey() method.
	}

	function setBitrix24IntegrationId(string $id): void {
		// TODO: Implement setBitrix24IntegrationId() method.
	}

	private function getBitrix24Model(): array {
		return [
			'bitrix24_integration_id' => $this->getBitrix24IntegrationId(),
		];
	}
}