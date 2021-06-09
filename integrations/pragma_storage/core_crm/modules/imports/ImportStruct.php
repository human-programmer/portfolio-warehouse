<?php


namespace PragmaStorage;


use Generals\Functions\Date;

require_once __DIR__ . '/../../business_rules/imports/IImportStruct.php';

class ImportStruct implements IImportStruct {
	private int $store_id;
	private int $number;
	private int $import_id;
	private string $date;
	private int $import_time;
	private int $time_create;
	private int $source;
	private string $provider;
	private bool $deleted;

	public function __construct(array $model) {
		$this->import_id = $model['import_id'] ?? 0;
		$this->date = $model['import_date'];
		$this->import_time = Date::getIntTimeStamp($model['import_date']);
		$this->time_create = Date::getIntTimeStamp($model['date_create']);
		$this->provider = $model['provider'];
		$this->source = $model['source'];
		$this->store_id = $model['store_id'];
		$this->number = $model['number'] ?? 0;
		$this->deleted = $model['deleted'] ?? false;
	}

	function getStoreId(): int {
		return $this->store_id;
	}

	function setStoreId(int $id): void {
		$this->store_id = $id;
	}

	function getImportId(): int {
		return $this->import_id;
	}

	function getTimeCreate(): int {
		return $this->time_create;
	}

	function setDate(string $date): void {
		$this->date = $date;
		$this->import_time = Date::getIntTimeStamp($date);
	}

	function getImportTime(): int {
		return $this->import_time;
	}

	function setImportTime(int $time): void {
		$this->date = Date::getStringTimeStamp($time);
		$this->import_time = $time;
	}

	function getSource(): int {
		return $this->source;
	}

	function isDeficit(): bool {
		return $this->getSource() === DEFICIT_SOURCE;
	}

	function getNumber(): int {
		return $this->number;
	}

	function isDeleted(): bool {
		return $this->deleted;
	}

	function getProvider(): string {
		return $this->provider;
	}

	function toArray(): array {
		return [
			'import_id' => $this->getImportId(),
			'import_date' => $this->date,
			'import_time' => $this->getImportTime(),
			'provider' => $this->getProvider(),
			'store_id' => $this->getStoreId(),
			'number' => $this->getNumber(),
			'source' => $this->getSource(),
			'is_deficit' => $this->isDeficit(),
			'deleted' => $this->isDeleted(),
		];
	}

	function setProvider(string $provider): void {
		$this->provider = $provider;
	}

	protected function setIsDeleted(): void {
		$this->deleted = true;
	}
}