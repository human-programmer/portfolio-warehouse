<?php


namespace PragmaStorage;



require_once __DIR__ . '/../../../business_rules/product_imports/iProductImport.php';
require_once __DIR__ . '/ProductImportModel.php';
require_once __DIR__ . '/ProductImportQuantity.php';

class ProductImport extends ProductImportModel implements iProductImport {
	use ProductImportQuantity;

	private ProductImports $product_imports;
	private int $date_create;
	private int|null $store_id;

	function __construct(private IStoreApp $app, ProductImports $product_imports, array $model) {
		$this->checkBalanceVar($model);
		parent::__construct($model);
		$this->product_imports = $product_imports;
	}

	function findImport(): iImport|null {
		return $this->getImportId() ? self::getImports()->getImport($this->getImportId()) : null;
	}

	function getProduct(): iProduct {
		return self::getProducts()->getProduct($this->getProductId());
	}

	function getExportDetails(): array {
		return PragmaFactory::getExportDetails()->getProductImportExportDetails($this);
	}

	function getOwnedExports(): array {
		$details = $this->getExportDetails();
		foreach ($details as $detail)
			$exports[] = $detail->getExport();
		return $exports ?? [];
	}

	function getExportQuantity(): float {
		return $this->getImportQuantity() - $this->getFreeBalanceQuantity();
	}

	function delete(): bool {
		if ($this->isExported())
			throw new \Exception('Невозможно удалить партию товара, который уже отправлен клиенту.');

		$exports = $this->getOwnedExports();
		$details = $this->getExportDetails();

		$this->getProductImports()->deleteProductImport($this);

		$this->setIsDeleted();

		foreach ($details as $detail)
			$detail->delete();

		return self::update_exports($exports);
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	private static function update_exports(array $exports): bool {
		foreach ($exports as $export)
			if (!$export->updateDetails())
				throw new \Exception('Failed to update export details for ' . $export->getExportId());
		return true;
	}

	private function updateExports(): bool {
		return self::update_exports($this->getOwnedExports());
	}

	function isExported(): bool {
		$exports = $this->getOwnedExports();

		foreach ($exports as $export)
			if ($export->isExported())
				return true;

		return false;
	}

	private static function getImports(): iImports {
		return PragmaFactory::getImports();
	}

	private static function getProducts(): iProducts {
		return PragmaFactory::getProducts();
	}

	private function getProductImports(): ProductImports {
		return $this->product_imports;
	}

	function update(array $model): bool {
		foreach ($model as $field_name => $val)
			switch ($field_name) {
				case 'quantity':
					$this->setQuantity($model['quantity']);
					break;
				case 'purchase_price':
					$this->setPurchasePrice($model['purchase_price']);
					break;
			}

		return $this->save();
	}

    function setPurchasePrice(float $price): void {
        parent::setPurchasePrice($price);
        $this->saveSelf();
    }

	function save(): bool {
		$this->saveSelf();
		return !$this->isDeficit() && $this->updateExports();
	}

	private function saveSelf(): void {
		$this->product_imports->saveProductImport($this);
	}

	function findStoreId(): null|int {
		if (isset($this->store_id))
			return $this->store_id;
		$this->store_id = $this->findImport()?->getStoreId();
		return $this->store_id;
	}

	function updateBalance(): void {
		if($this->isDeleted()) {
			parent::setFreeBalanceQuantity(0);
			parent::setBalanceQuantity(0);
			parent::setImportQuantity(0);
			return;
		}
		$quantities = ProductImportSchema::updateBalance($this->getProductImportId());
		parent::setFreeBalanceQuantity($quantities['free_balance_quantity']);
		parent::setBalanceQuantity($quantities['balance_quantity']);
		parent::setImportQuantity($quantities['import_quantity']);
	}

	private function saveQuantityAndUpdateBalance(): void {
		$quantities = ProductImportSchema::updateBalanceWithImportQuantity($this->getProductImportId(), $this->getImportQuantity());
		parent::setFreeBalanceQuantity($quantities['free_balance_quantity']);
		parent::setBalanceQuantity($quantities['balance_quantity']);
		parent::setImportQuantity($quantities['import_quantity']);
	}

	private function checkBalanceVar(array $model): void {
		if(is_null($model['free_balance_quantity']) || is_null($model['balance_quantity'])) {
			$this->updateBalance();
			$model['free_balance_quantity'] = $this->getFreeBalanceQuantity();
			$model['balance_quantity'] = $this->getBalanceQuantity();
			$model['import_quantity'] = $this->getImportQuantity();
		}
	}

	private function getSelf(): self {
		return $this;
	}

	function getStoreApp(): IStoreApp {
		return $this->app;
	}

	function getExportItems(): array {
		$id = $this->getProductImportId();
		return $this->getProductImports()->getExportsOfImports([$id])[$id] ?? [];
	}
}

	/*
	 SELECT @export := (SELECT
                SUM(quantity)
                   FROM `product_export_details`
                   WHERE `product_import_id` = 733
                   GROUP BY `product_import_id`) exports;

	SELECT @import := (SELECT
						quantity
					   FROM `product_imports`
					   WHERE `id` = 733) imports;

	UPDATE `product_imports` SET `balance_quantity` = @import - IF(@export IS NULL, 0, @export) WHERE `id` = 733
	 */