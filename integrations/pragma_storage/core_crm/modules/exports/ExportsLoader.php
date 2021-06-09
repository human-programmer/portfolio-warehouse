<?php


namespace PragmaStorage;

require_once __DIR__ . '/ExportsBuffer.php';

trait ExportsLoader {
	use ExportsBuffer;

	private function getTravelExports(int $travel_id): array {
		if($this->isTravelsPreloadedInBuffer($travel_id))
			return $this->getTravelExportsFromBuffer($travel_id);
		$this->loadTravelsExports($travel_id);
		return $this->getTravelExportsFromBuffer($travel_id);
	}

	private function loadTravelsExports(int $travel_id): void {
		$models = $this->getTravelExportModels([$travel_id]);
		foreach ($models as $model)
			$this->createAndSaveInBuffer($model);
		$this->setTravelsPreloadedInBuffer([$travel_id]);
	}
}