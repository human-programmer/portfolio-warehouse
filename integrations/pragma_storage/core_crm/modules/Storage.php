<?php


namespace PragmaStorage;


require_once __DIR__ . '/StoreApp.php';
require_once __DIR__ . '/../business_rules/iStorage.php';


class Storage extends StoreApp implements iStorage {
	static private int $instances_counter = 0;
	static private array $exports = [];

	public function __construct(int $pragma_account_id) {
		self::$instances_counter++;
		parent::__construct($pragma_account_id);
	}

	function createCategory(string $title): iCategory {
		return $this->getCategories()->createCategory($title);
	}

	function getCategory(int $category_id): iCategory {
		return $this->getCategories()->getCategory($category_id);
	}

	function deleteCategory(int $category_id): bool {
		return $this->getCategories()->getCategory($category_id)->delete();
	}

	function createStore(string $title, string $address): iStore {
		return $this->getStores()->createStore($title, $address);
	}

	function getStore(int $store_id): iStore {
		return $this->getStores()->getStore($store_id);
	}

	function deleteStore(int $store_id): bool {
		return $this->getStores()->getStore($store_id)->delete();
	}

	function deleteProduct(int $product_id): bool {
		$product = $this->getProducts()->getProduct($product_id);
		self::addExportsInBuffer($product->getExports());
		$flag = $product->delete();
		$this->changeEntitiesTrigger();
		return $flag;
	}

	function createProduct(int $category_id, string $article, string $title, float $selling_price, array $model = []): iProduct {
		return $this->getProducts()->createProduct($category_id, $article, $title, $selling_price, $model);
	}

	function getProduct(int $product_id): iProduct {
		return $this->getProducts()->getProduct($product_id);
	}

	function createImport(int $store_id, array $model): iImport {
		$store = $this->getStore($store_id);
		return $this->getImports()->createImport($store, $model);
	}

	function getImport(int $import_id): iImport {
		return $this->getImports()->getImport($import_id);
	}

	function deleteImport(int $import_id): bool {
		return $this->getImport($import_id)->delete();
	}

	function createProductImport(int $import_id, int $product_id, float $quantity, float $purchase_price): iProductImport {
		$product_import = $this->getProductImports()->createProductImport($import_id, $product_id, $quantity, $purchase_price);
		self::addExportsInBuffer($product_import->getOwnedExports());
		$this->changeEntitiesTrigger();
		return $product_import;
	}

	function getProductImport(int $product_import_id): iProductImport {
		return $this->getProductImports()->getProductImport($product_import_id);
	}

	function deleteProductImport(int $product_import_id): bool {
		$product_import = $this->getProductImport($product_import_id);
		self::addExportsInBuffer($product_import->getOwnedExports());
		$flag = $product_import->delete();
		$this->changeEntitiesTrigger();
		return $flag;
	}

	function createPragmaExport(int $pragma_entity_id, int $product_id, float $quantity, float $selling_price): iExport {
		$product = $this->getProducts()->getProduct($product_id);
		$entity = $this->getEntities()->getEntity($pragma_entity_id);
		$export = $this->getExports()->createExport($entity, $product, $quantity, $selling_price);

		$this->setStatusesForExports([$export], $entity);
		$this->changeExportTrigger($export);

		return $export;
	}

	function createPragmaExports(int $pragma_entity_id, array $models): array {
		$entity = $this->getEntities()->getEntity($pragma_entity_id);
		$export_models = $this->createExportsModes($models);
		$exports = $this->getExports()->createExports($entity, $export_models);
		$this->setStatusesForExports($exports, $entity);

		$exports[0] && $this->changeExportTrigger($exports[0]);

		return $exports;
	}

	private function createExportsModes(array $input_models): array {
		foreach ($input_models as $model)
			$answer[] = $this->createExportModel($model);
		return $answer ?? [];
	}

	private function createExportModel(array $input_model): array|null {
		$product = $this->getProducts()->getProduct($input_model['product_id']);
		return [
			'product' => $product,
			'quantity' => $input_model['quantity'],
			'selling_price' => $input_model['selling_price'],
			'priority_store_id' => $input_model['priority_store_id'],
		];
	}

	private function setStatusesForExports(array $exports, iEntity $entity): void {
		$status = $entity->getExportStatus() ?? $this->getStatuses()->getStatusByCode('linked');
		foreach ($exports as $export)
			$status && $export->setStatus($status->getStatusId());
	}

	function getExport(int $export_id): iExport {
		$export = $this->getExports()->getExport($export_id);
		self::addInBuffer($export);
		return $export;
	}

	static private function addExportsInBuffer (array $exports) : void {
		foreach ($exports as $export)
			self::addInBuffer($export);
	}

	static private function addInBuffer(iExport $export) : void {
		self::$exports[] = $export;
		if(!self::issetInBuffer($export))
			self::$exports[] = $export;
	}

	static private function issetInBuffer (iExport $export) : bool {
		foreach (self::$exports as $buffer_export)
			if($export->getEntityId() === $buffer_export->getEntityId() && $export->getProductId() === $buffer_export->getProductId())
				return true;
		return false;
	}

	function deleteExport(int|array $exports_id): bool {
		$exports_id = is_array($exports_id) ? $exports_id : [$exports_id];
		$export = $this->getExport($exports_id[0]);
		$flag = true;
		foreach ($exports_id as $id)
			$flag && $this->deleteExportRow($id);
		$this->changeExportTrigger($export);
		return $flag;
	}

	private function deleteExportRow(int $export_id): bool {
		$export = $this->getExport($export_id);
		return $export->delete();
	}

	function changeProductImportsTrigger (array $product_imports) : void {
		foreach ($product_imports as $product_import)
			self::addExportsInBuffer($product_import->getOwnedExports());
		count($product_imports) && $this->changeEntitiesTrigger();
	}

	function changeEntitiesTrigger() : void {
		foreach (self::$exports as $export)
			$this->setChangedValue($export);
		self::$exports = [];
		self::changeTrigger();
	}

	private function changeExportTrigger (iExport $export) : void {
		$this->setChangedValue($export);
		self::changeTrigger();
	}

	private function setChangedValue (iExport $export) : void {
		$entity = $this->getEntities()->getEntity($export->getEntityId());
		$entity->setChangedExportValues();
	}

	static private function changeTrigger() : void {
		try {
			PragmaFactory::getActions()->changeValuesTrigger();
		} catch (\Exception $e) {
			Factory::getLogWriter()->send_error($e);
		}
	}

	function setStatusLinks(array $links): bool {
		return $this->getStatusToStatus()->setExportStatusLinks($links);
	}

	function getStatusLinks(): array {
		return $this->getStatusToStatus()->getLinks();
	}

	function getExportStatuses(): array {
		return $this->getStatuses()->getStatusModels();
	}
}

