<?php


namespace PragmaStorage;


use Generals\Functions\Date;

class TravelsSchema extends PragmaStoreDB {
	function __construct(private int $account_id) {
		parent::__construct();
	}

	protected function insertTravel(ICreationTravelModel $model, int $import_id): int {
		$travels = self::getStorageTravelsSchema();
		$sql = "INSERT INTO $travels (start_store_id, end_store_id, end_import_id, user_id, travel_date, travel_status)
				VALUES (:start_store_id, :end_store_id, :end_import_id, :user_id, :travel_date, :travel_status)";
		self::executeSql($sql, [
			'start_store_id' => $model->getStartStoreId(),
			'end_store_id' => $model->getEndStoreId(),
			'end_import_id' => $import_id,
			'user_id' => $model->getUserId(),
			'travel_date' => Date::getStringTimeStamp($model->getTravelDate()),
			'travel_status' => EXPORT_STATUS_EXPORTED,
		]);
		return self::last_id();
	}

	protected function getTravelModel(int $id): array {
		$result = self::querySql(self::sql("id = $id"));
		return $result[0] ?? throw new \Exception("Travel '$id' not found");
	}

	private static function sql(string $condition): string {
		$travels = self::getStorageTravelsSchema();
		return "SELECT
					id as travel_id,
       				start_store_id,
       				end_store_id,
					end_import_id,
					user_id,
					travel_date,
					creation_date,
					travel_status
				FROM $travels 
				WHERE $condition";
	}
}