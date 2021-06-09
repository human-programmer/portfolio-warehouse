<?php


namespace PragmaStorage;


trait ExportsBuffer {
	private array $exports = [];
	private array $travels_preloaded = [];


	private function findInBuffer(int $export_id): iExport|null {
		return $this->exports[$export_id] ?? null;
	}

	private function findInBufferByEntity(int $entity_id, int $product_id) {
		foreach ($this->exports as $export)
			if ($export->getPragmaEntityId() === $entity_id && $export->getProductId() === $product_id)
				return $export;
		return null;
	}

	private function getTravelExportsFromBuffer(int $travel_id): array {
		foreach ($this->exports as $export)
			if($export->finTravelLink()?->getTravelId() === $travel_id)
				$result[] = $export;
		return $result ?? [];
	}

	private function addInBuffer(iExport $export): void {
		$this->exports[$export->getExportId()] = $export;
	}

	private function setTravelsPreloadedInBuffer(array $travel_id): void {
		foreach ($travel_id as $id)
			$this->travels_preloaded[$id] = $id;
	}

	private function isTravelsPreloadedInBuffer(int $travel_id): bool {
		return isset($this->travels_preloaded[$travel_id]);
	}
}