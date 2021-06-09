<?php


namespace PragmaStorage;


trait ProductImportsLoader {
	use TProductImportBuffer;

	private function preloadTargetProductsImports(array $id): void {
	    $id = $this->filterExistsProductsImports($id);
        $models = $this->loadProductImportsFromDb($id);
        foreach ($models as $model)
            $this->createInstance($model);
    }

	private function loadStoreDeficit(int $product_id, int $store_id): void {
		$deficitImport = $this->app->getImports()->getDeficitImport($store_id);
		$model = $this->getProductDeficitModel($product_id, $deficitImport->getImportId());
		$this->createInstance($model);
	}

	private function getFromDb(int $product_import_id): iProductImport {
		$model = $this->get_product_import($product_import_id);
		return $this->createInstance($model);
	}

	private function loadFromDb(int $product_id): void {
		$this->preloadProductImports([$product_id]);
	}

	function preloadProductImports(array $product_id): void {
		$models = $this->getModels($product_id);
		foreach ($models as $model)
			$this->createInstance($model);
		$this->setBufferPreloadedProducts($product_id);
	}

	private static function filterProductImportsByStore(array $productImports, int|array $store_id): array {
		if(is_null($store_id)) return array_merge([], $productImports);
		$store_id = is_array($store_id) ? $store_id : [$store_id];
		foreach ($productImports as $productImport)
			if(self::idIn($store_id, $productImport->findStoreId()))
				$result[] = $productImport;
		return $result ?? [];
	}

	private static function idIn(array $hayStack, int|null $id): bool {
		if(is_null($id)) return false;
		return array_search($id, $hayStack) !== false;
	}

	function getExportsOfImports(array $productImportId): array {
		$exports_id = ProductImportSchema::getLinkedExportsId($productImportId);
		$id = array_merge(...array_values($exports_id));
		$exports = $this->app->getExports()->getExports($id);
		return self::groupExports($exports_id, $exports);
	}

	private static function groupExports(array $exports_id, array $exports): array {
		foreach ($exports as $export)
			$grouped_exports[$export->getExportId()] = $export;

		foreach ($exports_id as $product_import_id => $ids) {
			$result[$product_import_id] = [];
			foreach ($ids as $export_id)
				if(isset($grouped_exports[$export_id]) && $grouped_exports[$export_id])
					$result[$product_import_id][] = $grouped_exports[$export_id];
		}
		return $result ?? [];
	}
}