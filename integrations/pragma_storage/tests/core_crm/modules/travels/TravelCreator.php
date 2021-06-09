<?php


namespace PragmaStorage\Test;


use PragmaStorage\ITravel;
use PragmaStorage\ITravelModel;
use PragmaStorage\TravelModel;
use PragmaStorage\Travels;

require_once __DIR__ . '/../../../../core_crm/modules/travels/travel/TravelModel.php';

trait TravelCreator {
	use StoresCreator, UsersCreator;
	private static array $test_travels = [];

	static function uniqueTravel(): ITravel {
		$travel_model = self::uniqueTravelModel();
		$travels = new Travels(TestPragmaFactory::getStoreApp());
		$travel = $travels->createTravel($travel_model);
		self::$test_travels[] = $travel->getTravelId();
		return $travel;
	}

	static function uniqueTravelModel(): ITravelModel {
	    $end_store = self::getUniqueStore();
		$model = [
			'start_store_id' => self::getUniqueStore()->getStoreId(),
			'end_store_id' => $end_store->getStoreId(),
			'end_import_id' => self::getUniqueImport($end_store)->getImportId(),
			'user_id' => self::uniqueUserId(),
			'travel_date' => rand(1, 999999999),
		];
		return new TravelModel($model);
	}

	static function clearTravels(): void {
		self::clearUsers();
		self::clearStores();
	}
}