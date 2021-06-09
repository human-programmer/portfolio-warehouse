<?php


namespace PragmaStorage;

require_once __DIR__ . '/travel/Travel.php';
require_once __DIR__ . '/../../business_rules/travels/ITravels.php';
require_once __DIR__ . '/TravelsSchema.php';
require_once __DIR__ . '/TravelsBuffer.php';


class Travels extends TravelsSchema implements ITravels{
	use TravelsBuffer;

	function __construct(private IStoreApp $app) {
		parent::__construct($this->app->getPragmaAccountId());
	}

	function getTravel(int $travel_id): ITravel {
		return $this->findTravelInBuffer($travel_id) ?? $this->getFromDb($travel_id);
	}

	private function getFromDb(int $travel_id): ITravel {
		$model = self::getTravelModel($travel_id);
		return self::createInstance($model);
	}

	function createTravel(ICreationTravelModel $model): ITravel {
		$import_id = $this->app->getImports()->createTravelsImport($model)->getImportId();
		$id = $this->insertTravel($model, $import_id);
		return $this->getTravel($id);
	}

	function cancelTravel(int $travel_id): void {
		// TODO: Implement cancelTravel() method.
	}

	private function createInstance(array $model): ITravel {
		$travel = $this->findTravelInBuffer($model['travel_id']) ?? new Travel($this->app, $this, $model);
		$this->addTravelInBuffer($travel);
		return $travel;
	}
}