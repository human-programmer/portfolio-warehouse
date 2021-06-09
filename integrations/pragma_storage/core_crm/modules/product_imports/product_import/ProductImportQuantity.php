<?php


namespace PragmaStorage;


trait ProductImportQuantity {

	function setQuantity(float $quantity): void {
	    $this->validQuantity($quantity);
		$this->changeQuantity($quantity);
	}

	private function validQuantity(float $quantity): void {
        $exported_quantity = $this->getExportedQuantity();
        if (isset($quantity) && (float)$quantity < $exported_quantity)
            throw new \Exception("Часть товара из этой партии ($exported_quantity) уже оправлена клиенту", PART_OF_THE_GOODS_SENT_TO_THE_CUSTOMER);
    }

	function getExportedQuantity(): float {
		$details = $this->getExportDetails();
		$result = 0.0;
		foreach ($details as $detail)
			if ($detail->isExported())
				$result += $detail->getQuantity();
		return $result;
	}

	private function changeQuantity(float $new_quantity): void {
		if($new_quantity === $this->getImportQuantity()) return;

		if($this->getImportQuantity() < $new_quantity)
			$this->enlargeQuantity($new_quantity);
		else if ($this->getImportQuantity() > $new_quantity)
			$this->reduceQuantity($new_quantity);
	}

	private function enlargeQuantity(float $new_quantity): void {
		$this->saveNewQuantity($new_quantity);
		$exports = $this->getDeficitExportsByPriority();
		foreach ($exports as $export)
			$this->getFreeBalanceQuantity() > 0 && $export->updateDetails($this->getSelf());
	}

	private function getDeficitExportsByPriority(): array {
		$exports = $this->getDeficitExports($this->getProductId());
		return $this->sortExportsByPriority($exports);
	}

	function getDeficitExports(int $product_id): array {
		return $this->getStoreApp()->getExports()->getDeficitExports($product_id);
	}

	private function reduceQuantity(float $new_quantity): void {
		$this->saveNewQuantity($new_quantity);
		if($this->getFreeBalanceQuantity() >= 0) return;
		$exports = $this->getOwnExportsSortedToReduce();
		foreach ($exports as $export)
			$this->getFreeBalanceQuantity() < 0 && $export->updateDetails($this->getSelf());
	}

	private function saveNewQuantity(float $newQuantity): void {
		parent::setQuantity($newQuantity);
		$this->saveQuantityAndUpdateBalance();
	}

	private function getOwnExportsSortedToReduce(): array {
		$exports = $this->getOwnExportsByPriority();
		return self::sortExportsToReduce($exports);
	}

	private function getOwnExportsByPriority(): array {
		$exports = $this->getExportItems();
		return $this->sortExportsByPriority($exports);
	}

	private function sortExportsByPriority(array $exports): array {
		$iterator = $this->createIterator();
		return $iterator->sortExports($exports, $this->findStoreId() ?? 0);
	}

	private function createIterator(): IStorePriorityIterator {
		return $this->getStoreApp()->getStorePriorities()->createIteratorForProductImport($this->getSelf());
	}

	private static function sortExportsToReduce(array $exports_by_priority): array {
		$exports_by_priority = array_reverse($exports_by_priority);
		foreach ($exports_by_priority as $export)
			if($export->isExported())
				$exported[] = $export;
			else
				$first[] = $export;
		return array_merge($first ?? [], $exported ?? []);
	}
}