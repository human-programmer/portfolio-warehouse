<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/statuses/iStatus.php';


class Status implements iStatus {
	private int $status_id;
	private string $status_code;
	private string $title;

	public function __construct(int $status_id, string $status_code, string $title) {
		$this->status_id = $status_id;
		$this->status_code = $status_code;
		$this->title = $title;
	}

	function getStatusId(): int {
		return $this->status_id;
	}

	function getStatusCode(): string {
		return $this->status_code;
	}

	function getStatusTitle(): string {
		return $this->title;
	}

	function isDetailed(): bool {
		switch ($this->status_code) {
			case 'reserved':
			case 'exported':
				return true;
			case 'linked':
				return false;
		}

		throw new \Exception('Unknown status code: ' . $this->status_code);
	}

	function isExported(): bool {
		switch ($this->status_code) {
			case 'linked':
			case 'reserved':
				return false;
			case 'exported':
				return true;
		}

		throw new \Exception('Unknown status code: ' . $this->status_code);
	}

	function isDeleted(): bool {
		return false;
	}

	function delete(): bool {
		return false;
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		return ['id' => $this->getStatusId(), 'code' => $this->getStatusCode(), 'title' => $this->getStatusTitle()];
	}

	function update(array $model): bool {
		return false;
	}
}