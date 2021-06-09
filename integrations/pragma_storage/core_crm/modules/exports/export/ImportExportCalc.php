<?php


namespace PragmaStorage;


trait ImportExportCalc {
	private array $currentProductsImports = [];

	private function setStageCurrentImports(): void {
		$productsImports = $this->getProductsImports();
		foreach ($productsImports as $productsImport)
			$this->currentProductsImports[$productsImport->getProductImportid()] = $productsImport;
	}

	private function updateProductsImportsBalance(): void {
		$productImports = $this->getProductsImportsToUpdateAndClear();
		foreach ($productImports as $productsImport)
			$productsImport->updateBalance();
	}

	private function getProductsImportsToUpdateAndClear(): array {
		$productsImports = $this->getProductsImports();
		foreach ($productsImports as $productsImport)
			$this->currentProductsImports[$productsImport->getProductImportid()] = $productsImport;
		$result = $this->currentProductsImports;
		$this->currentProductsImports = [];
		return $result;
	}

	private function updateDifDetails(iProductImport $productImport = null): bool {
		$this->setStageCurrentImports();
		$flag = $productImport && $this->updateTargetImportDetail($productImport);
		$flag = $flag || $productImport || $this->updateAllDifDetails();
		$this->updateProductsImportsBalance();
		return $flag;
	}

	private function updateTargetImportDetail(iProductImport $productImport): bool {
		$free = $productImport->getFreeBalanceQuantity();
		$free < 0 && $this->replaceTargetImportDetail($productImport);
		$free > 0 && $this->replaceExportDeficit($productImport);
		return true;
	}

	private function replaceTargetImportDetail(iProductImport $productImport): void {
		$reduced = $this->reduceTargetImportDetail($productImport);
		$this->enlargeDeficitDetail(abs($reduced));
	}

	private function reduceTargetImportDetail(iProductImport $productImport): float {
		$detail = $this->findDetail($productImport->getProductImportId());
		if(!$detail) return 0.0;
		$dif = abs($this->getDifQuantity($productImport));
		$reduced = $dif > $detail->getQuantity() ? $detail->getQuantity() : $dif;
		$detail->reduceQuantity($dif);
		return $reduced;
	}

	private function replaceExportDeficit(iProductImport $productImport): void {
		$free = $this->getDifQuantity($productImport);
		$reduced = $this->reduceDeficitDetailsAndGetReducedQuantity($free);
		$res = $this->createOrGetDetail($productImport)->addQuantity($reduced);
		if((float) $res !== 0.0)
			throw new \Exception("Invalid addQuantity answer");
	}

	private function getDifQuantity(iProductImport $productImport): float {
		$dif = $productImport->getFreeBalanceQuantity();
		if($dif >= 0) return $dif;
		if(abs($dif) > $this->getQuantity())
			return $this->getQuantity() * -1;
		return $dif;
	}

	private function updateAllDifDetails(): bool {
		$dif = $this->getDetailsQuantity() - $this->getQuantity();
		if (!$dif) return true;
		if ($dif > 0) return $this->reduceDetails(abs($dif));
		return $this->enlargeDetails(abs($dif));
	}

	private function setChangedExportValues() : void {
		try{
            $this->getEntityId() && PragmaFactory::getEntities()->getEntity($this->getEntityId())->setChangedExportValues();
		} catch (\Exception $e) {
			PragmaFactory::getLogWriter()->send_error($e);
		}
	}

	private function enlargeDetails(float $quantity) {
		$quantity = $this->enlargeCurrentDetails($quantity);
		$quantity = $this->enlargeFreDetails($quantity);
		$this->enlargeDeficitDetail($quantity);
		return true;
	}

	private function enlargeCurrentDetails(float $quantity): float {
		$details = $this->getDetails();
		foreach ($details as $detail)
			if (!$detail->isDeficit())
				if (($quantity = $detail->addQuantity($quantity)) <= 0)
					return 0;
		return $quantity;
	}

	private function enlargeFreDetails(float $quantity): float {
		$free_detail = $this->findFreeDetail();
		while ($quantity > 0 && $free_detail) {
			if($quantity <= 0) return 0;
			$quantity = $free_detail->addQuantity($quantity);
			$free_detail = $this->findFreeDetail();
		}
		return $quantity;
	}

	private function enlargeDeficitDetail(float $quantity): void {
		$detail = $this->createOrGetDeficitDetail();
		$quantity && $detail->addQuantity($quantity);
	}

	private function findFreeDetail() {
		$product_import = $this->findFreeProductImport();
		return $product_import ? $this->createOrGetDetail($product_import) : null;
	}

	private function createOrGetDetail(iProductImport $productImport): iExportDetail {
		return self::getExportDetails()->getExportDetail($this, $productImport);
	}

	private function findFreeProductImport(): iProductImport|null {
		$priorityIterator = $this->createPrioritiesIterator();
		foreach ($priorityIterator as $priority) {
			$freeProductImport = $this->findFreeProductImportForStore($priority->getStoreId());
			if($freeProductImport) return $freeProductImport;
		}
		return null;
	}

	private function findFreeProductImportForStore(int $store_id): iProductImport|null {
		return $this->getStoreApp()->getProductImports()->findFreeProductImport($this->getProductId(), $store_id);
	}

	private function reduceDetails(float $quantity) {
		if ($quantity >= $this->getDetailsQuantity())
			return $this->clearDetails();
		$quantity = $this->reduceDeficitDetails($quantity);
		return $this->reduceOtherDetails($quantity) && throw new \Exception('Unknown error');
	}

	private function reduceDeficitDetails(float $quantity): float {
		$deficits = array_reverse($this->getDeficitDetails());
		foreach ($deficits as $deficit)
			if (($quantity = $deficit->reduceQuantity($quantity)) <= 0)
				return 0;

		return $quantity;
	}

	private function reduceDeficitDetailsAndGetReducedQuantity(float $quantity): float {
		$deficits = array_reverse($this->getDeficitDetails());
		$reduced = 0.0;

		foreach ($deficits as $deficit) {
			$dif = $quantity - $deficit->getQuantity();
			if($dif >= 0)
				$reduced += $deficit->getQuantity();
			else
				$reduced += $quantity;
			if (($quantity = $deficit->reduceQuantity($quantity)) <= 0)
				return $reduced;
		}
		return $reduced;
	}

	function getDeficitDetails(): array {
		$details = $this->getDetails();
		foreach ($details as $detail)
			if ($detail->isDeficit())
				$result[] = $detail;
		return $this->sortDetailsByPriority($result ?? []);
	}

	private function reduceOtherDetails(float $quantity): float {
		$details = array_reverse($this->getDetails());
		foreach ($details as $detail) {
			if (($quantity = $detail->reduceQuantity($quantity)) <= 0)
				return 0;
		}
		return $quantity;
	}

	function findDetail(int $productImportId): iExportDetail|null {
		$details = $this->getDetails();
		foreach ($details as $detail)
			if($detail->getProductImportId() === $productImportId)
				return $detail;

		return null;
	}

	function getDetails(): array {
		$details = self::getExportDetails()->getExportDetails($this);
		return $this->sortDetailsByPriority($details);
	}

	private function sortDetailsByPriority(array $details): array {
		if(!count($details)) return $details;
		foreach ($this->createPrioritiesIterator() as $priority)
			$result = array_merge($result ?? [], $this->fetchDetails($details, $priority->getStoreId()));
		return array_merge($result ?? [], $details);
	}

	private function fetchDetails(array &$details, int $store_id): array {
		foreach ($details as $index => $detail)
			if($this->findStoreId($detail) === $store_id) {
				$res[] = $detail;
				unset($details[$index]);
			}
		return $res ?? [];
	}

	private function findStoreId(iExportDetail $detail): int|null {
		return $detail->getProductImport()->findImport()?->getStoreId();
	}

	private function createOrGetDeficitDetail(): iExportDetail {
		$productImport = $this->getDeficitProductImport();
		return PragmaFactory::getExportDetails()->getExportDetail($this->getSelf(), $productImport);
	}

	private function getDeficitProductImport(): iProductImport {
		return $this->findExistsDeficit() ?? $this->getHighestPriorityDeficit();
	}

	private function findExistsDeficit(): iProductImport|null {
		return $this->findExistsLinkedDeficit() ?? $this->findExistsLinkedStoreDeficit();
	}

	private function findExistsLinkedDeficit(): iProductImport|null {
		return $this->getExistsDeficits()[0] ?? null;
	}

	private function getExistsDeficits(): array {
		$productImports = $this->getProductsImports();
		foreach ($productImports as $productImport)
			if($productImport->isDeficit())
				$result[] = $productImport;
		return $this->sortProductImportsByPriority($result ?? []);
	}

	private function findExistsLinkedStoreDeficit(): iProductImport|null {
		$product_import = $this->findExistsHighestPriorityLinkedImport();
		$store_id = $product_import?->findStoreId();
		if(!$store_id) return null;
		return $this->getStoreApp()->getProductImports()->getProductDeficit($this->getProductId(), $store_id);
	}

	private function findExistsHighestPriorityLinkedImport(): iProductImport|null {
		$productImports = $this->getProductsImports();
		foreach ($productImports as $productImport)
			if(!$productImport->isDeficit())
				$result[] = $productImport;
		return $this->sortProductImportsByPriority($result ?? [])[0] ?? null;
	}

	private function sortProductImportsByPriority(array $productImports): array {
		$iterator = $this->createPrioritiesIterator();
		return $iterator->sortProductImports($productImports);
	}

	private function getHighestPriorityDeficit(): iProductImport {
		$store_id = $this->getHighestPriorityAllowedStore()->getStoreId();
		return $this->getStoreApp()->getProductImports()->getProductDeficit($this->getProductId(), $store_id);
	}

	private function getHighestPriorityAllowedStore(): iStore {
	    $highestStoreId = $this->getHighestPriority()->getStoreId();
        return $this->getStoreApp()->getStores()->getStore($highestStoreId);
	}

	private function createPrioritiesIterator(): IStorePriorityIterator {
		return $this->getStoreApp()->getStorePriorities()->createIterator($this->getSelf());
	}
}