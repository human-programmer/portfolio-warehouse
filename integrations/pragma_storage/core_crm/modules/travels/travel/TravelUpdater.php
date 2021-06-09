<?php


namespace PragmaStorage;

require_once __DIR__ . '/TravelCreatePrepare.php';

trait TravelUpdater {
	use TravelCreatePrepare;

	function addProduct(int $product_id, float $quantity): void {
		$this->updateImportExportPair($product_id, $quantity);
	}

	function addProducts(array $products): void {
		$this->initChangeTravelStage($products);
		$this->updatePairs();
		$this->resetNewAddedProducts();
	}

	private function updatePairs(): void {
		foreach ($this->getNewAddedProducts() as $product)
			$this->updateImportExportPair($product['product_id'], $product['quantity']);
	}

	private function updateImportExportPair(int $product_id, float $quantity): void {
		$link = $this->getStoreApp()->getTravelLinks()->getOrCreateTravelsLink($this->getTravelId(), $product_id);
        $link->setQuantity($quantity);
	}
}