<?php


namespace PragmaStorage;


trait TProductImportBuffer {
	private array $product_imports = [];
	private array $preloadedProducts = [];
	private array $preloadedTravels = [];

	private function filterExistsProductsImports(array $productsImportsId): array {
	    foreach ($productsImportsId as $id)
	        if(!isset($this->product_imports[$id]))
	            $result[] = $id;
        return $result ?? [];
    }

	private function addInBuffer(iProductImport $import): void {
		$this->product_imports[$import->getProductImportId()] = $import;
	}

	private function getFromBuffer(array $ids): array {
	    foreach ($ids as $id)
	        if(isset($this->product_imports[$id]))
	            $result[] = $this->product_imports[$id];
        return $result ?? [];
    }

	private function findInBuffer(int $id): iProductImport|null {
		return $this->product_imports[$id] ?? null;
	}

	private function findInBufferByProductId(int $product_id): array {
		foreach ($this->product_imports as $import)
			if($import->getProductId() === $product_id)
				$result[] = $import;
		return $result ?? [];
	}

	private function findInBufferDeficit(int $product_id, int $store_id): iProductImport|null {
		foreach ($this->product_imports as $import)
			if($import->isDeficit() && $import->getProductId() === $product_id && $import->findStoreId() === $store_id)
				return $import;
		return null;
	}

	private function deleteFromBuffer(int $id): void {
		unset($this->product_imports[$id]);
	}

	private function setBufferPreloadedProducts(array $product_id): void {
		foreach ($product_id as $id)
			$this->preloadedProducts[$id] = $id;
	}

	private function isBufferPreloadedProduct(int $product_id): bool {
		return isset($this->preloadedProducts[$product_id]);
	}

	function getProductImportsFromBuffer(): array {
		return array_merge([], $this->product_imports);
	}

	function getPreloadedProductsFromBuffer(): array {
		return array_merge([], $this->preloadedProducts);
	}

	private function isBufferPreloadedTravel(int $travel_id): bool {
		return isset($this->preloadedTravels[$travel_id]);
	}

	private function setBufferPreloadedTravels(array $travel_id): void {
		foreach ($travel_id as $id)
			$this->preloadedTravels[$id] = $id;
	}

	private function getTravelsProductImports(int $travel_id): array {
		foreach ($this->product_imports as $product_import)
			if($product_import->findProductTravel()?->getTravelId() === $travel_id)
				$result[] = $product_import;
		return $result ?? [];
	}
}