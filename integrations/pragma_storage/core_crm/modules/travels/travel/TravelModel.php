<?php


namespace PragmaStorage;


use Generals\Functions\Date;

require_once __DIR__ . '/../../../business_rules/travels/ITravelModel.php';

class TravelModel implements ITravelModel {

	private int $travel_id;
	private int $start_store_id;
	private int $end_store_id;
	private int $user_id;
	private int $travel_date;
	private int $end_import_id;

	public function __construct(array $model) {
		$this->travel_id = $model['travel_id'] ?? 0;
		$this->start_store_id = $model['start_store_id'];
		$this->end_store_id = $model['end_store_id'];
		$this->user_id = $model['user_id'];
		$this->travel_date = gettype($model['travel_date']) === "integer" ? $model['travel_date'] : Date::getIntTimeStamp($model['travel_date']);
		$this->end_import_id = $model['end_import_id'];
	}

	function getEndImportId(): int {
		return $this->end_import_id;
	}

	function getTravelId(): int {
		return $this->travel_id;
	}

	function getStartStoreId(): int {
		return $this->start_store_id;
	}

	function getEndStoreId(): int {
		return $this->end_store_id;
	}

	function getUserId(): int {
		return $this->user_id;
	}

	function getTravelDate(): int {
		return $this->travel_date;
	}

	function toArray(): array {
		return [
			'travel_id' => $this->getTravelId(),
			'start_store_id' => $this->getStartStoreId(),
			'end_store_id' => $this->getEndStoreId(),
			'end_import_id' => $this->getEndImportId(),
			'user_id' => $this->getUserId(),
			'travel_date' => $this->getTravelDate(),
		];
	}
}