<?php


namespace PragmaStorage;



require_once __DIR__ . '/../../business_rules/priorities/IStoreExportPriority.php';

class StorePriorityModel implements IStoreExportPriority {
	private int $export_id;
	private int $store_id;
	private int $sort;

	public function __construct(array $model) {
		$this->export_id = $model['export_id'];
		$this->store_id = $model['store_id'];
		$this->sort = $model['sort'];
	}
	function getExportId(): int {
		return $this->export_id;
	}
	function getStoreId(): int {
		return $this->store_id;
	}
	function getSort(): int {
		return $this->sort;
	}
	function toArray(): array {
		return [
			'export_id' => $this->export_id,
			'store_id' => $this->store_id,
			'sort' => $this->sort,
		];
	}
}