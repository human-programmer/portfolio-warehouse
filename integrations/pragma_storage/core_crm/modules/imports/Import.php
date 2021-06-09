<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../business_rules/imports/iImport.php';
require_once __DIR__ . '/ImportStruct.php';

class Import extends ImportStruct implements iImport {
	private Imports $imports;

	public function __construct(Imports $imports, array $model) {
		parent::__construct($model);
		$this->imports = $imports;
	}

	function delete(): bool {
		$this->checkSelfToDelete();
		$this->deleteProductImports();
		$this->deleteSelf();
		return true;
	}

	private function checkSelfToDelete(): void {
		if ($this->isExported())
			throw new \Exception('Невозможно удаление партии товаров, часть из которых уже отправлена клиенту');
	}

	private function deleteProductImports(): void {
		$product_imports = $this->getOwnedProductImports();
		foreach ($product_imports as $product_import)
			if (!$product_import->delete())
				throw new \Exception('Failed to delete ProductImport');
	}

	private function deleteSelf(): void {
		if (!$this->getImports()->deleteImport($this))
			throw new \Exception('Failed to delete Import');
		$this->setIsDeleted();
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function getOwnedProductImports(): array {
		return self::getProductImports()->getImportProductImports($this->getImportId());
	}

	function isExported(): bool {
		$product_imports = $this->getOwnedProductImports();

		foreach ($product_imports as $product_import)
			if ($product_import->isExported())
				return true;

		return false;
	}

	function update(array $model): bool {
		foreach ($model as $field_name => $val)
			switch ($field_name) {
				case 'store_id':
					$store = self::getStores()->getStore($val);
					$this->setStoreId($store->getStoreId());
					break;

				case 'import_date':
					$this->setDate($val);
					break;

				case 'provider':
					$this->setProvider($val);
					break;
			}
		$this->getImports()->save($this);
		return true;
	}

	private static function getProductImports(): iProductImports {
		return PragmaFactory::getProductImports();
	}

	private static function getStores(): iStores {
		return PragmaFactory::getStores();
	}

	private function getImports(): iImports {
		return $this->imports;
	}
}