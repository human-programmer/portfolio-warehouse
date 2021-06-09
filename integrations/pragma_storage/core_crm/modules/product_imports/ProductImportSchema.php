<?php


namespace PragmaStorage;



require_once __DIR__ . '/ProductImportBalanceUpdater.php';

class ProductImportSchema extends PragmaStoreDB {
	use ProductImportBalanceUpdater;

	protected function __construct(private int $pragma_account_id) {
		parent::__construct();
	}

	protected function getProductDeficitModel(int $product_id, int $deficit_import_id): array {
		$model = $this->findProductDeficitModel($product_id, $deficit_import_id);
		if ($model) return $model;
		$deficit_id = $this->createProductDeficitRow($product_id, $deficit_import_id);
		return $this->get_product_import($deficit_id);
	}
	private function createProductDeficitRow(int $product_id, int $deficit_import_id): int {
		$product_imports_schema = parent::getStorageProductImportsSchema();
		$source = DEFICIT_SOURCE;
		$sql = "INSERT INTO $product_imports_schema 
                (`product_id`, `import_id`, `quantity`, `purchase_price`, `free_balance`, `balance`, `source`)
                VALUES ($product_id, $deficit_import_id, 0, 0, 0,0, $source)";
		self::executeSql($sql);
		return self::last_id();
	}

	private function findProductDeficitModel(int $product_id, int $deficit_import_id): array|null {
		$product_imports = $this->getStorageProductImportsSchema();
		$condition = " WHERE $product_imports.`product_id` = $product_id AND $product_imports.`import_id` = $deficit_import_id";
		$sql = $this->sql_imports() . $condition;
		return self::querySql($sql)[0] ?? null;
	}

	protected function get_product_import(int $product_import_id): array {
		$product_imports = $this->getStorageProductImportsSchema();
		$condition = " WHERE $product_imports.`id` = $product_import_id GROUP BY $product_imports.`id`";
		$sql = $this->sql_imports() . $condition;
		return self::executeGetSql($sql)[0];
	}

	protected function get_import_product_imports(int $import_id): array {
		$product_imports = $this->getStorageProductImportsSchema();
		$condition = " WHERE $product_imports.`import_id` = $import_id";
		$sql = $this->sql_imports() . $condition;
		return self::executeGetSql($sql);
	}

	protected function loadProductImportsFromDb(array $import_ids): array {
		if (!count($import_ids)) return [];
		$product_imports = $this->getStorageProductImportsSchema();
		$str_val = implode(',', $import_ids);
		$condition = "$product_imports.import_id IN ($str_val)";
		$sql = $this->sql_imports() . " WHERE $condition";
		return self::executeGetSql($sql);
	}

	protected function delete_product_import(int $product_import_id): bool {
		$product_imports_schema = $this->getStorageProductImportsSchema();

		$sql = "DELETE FROM $product_imports_schema WHERE $product_imports_schema.`id` = $product_import_id";

		return self::execute($sql);
	}

	protected function getModels(array $product_id): array {
		if(!count($product_id)) return [];
		$idsStr = implode(',', $product_id);
		$product_imports = $this->getStorageProductImportsSchema();
		$sql = $this->sql_imports() . " WHERE $product_imports.`product_id` IN ($idsStr)";
		return self::executeGetSql($sql);
	}

	protected function findProductImport(int $product_id, int $import_id) {
		$product_imports = $this->getStorageProductImportsSchema();

		$sql = $this->sql_imports() . " WHERE $product_imports.`product_id` = $product_id
                                        AND $product_imports.`import_id` = $import_id";

		return self::executeGetSql($sql)[0] ?? null;
	}

	private static function executeGetSql(string $sql): array {
		return self::querySql($sql);
	}

	private static function sql_imports(): string {
		$product_imports = self::getStorageProductImportsSchema();
		$imports = self::getStorageImportsSchema();

		return "SELECT 
					$product_imports.`id` AS `product_import_id`,
					$product_imports.`product_id` AS `product_id`,
					$product_imports.`import_id` AS `import_id`,
					$product_imports.`purchase_price` AS `purchase_price`,
					$product_imports.`quantity` AS `import_quantity`,
					$product_imports.`free_balance` AS `free_balance_quantity`,
					$product_imports.`balance` AS `balance_quantity`,
					$product_imports.`source` AS `source`,
					$product_imports.`date_create` AS `date_create`,
					$imports.`date` AS `import_date`
                FROM $product_imports 
                	INNER JOIN $imports ON $imports.id = $product_imports.import_id
				";
	}

	protected function create_product_import(array $model): int {
		if ($this->findProductImport($model['product_id'], $model['import_id']))
			throw new \Exception('This product import already exists', PRODUCTS_IMPORT_ALREADY_EXISTS);
		$product_imports_schema = parent::getStorageProductImportsSchema();

		self::formattingModel($model);

		$sql = "INSERT INTO $product_imports_schema 
                (`product_id`, `import_id`, `quantity`, `purchase_price`, `free_balance`, `balance`)
                VALUES (:product_id, :import_id, :import_quantity, :purchase_price, :free_balance_quantity, :balance_quantity)";
		self::executeSql($sql, $model);
		return self::last_id();
	}

	protected function createNewProductImportRow(IProductImportModel $model): int {
		$product_imports_schema = parent::getStorageProductImportsSchema();
		$sql = "INSERT INTO $product_imports_schema 
                (`product_id`, `import_id`, `quantity`, `purchase_price`, `source`, `free_balance`, `balance`)
                VALUES (:product_id, :import_id, :import_quantity, :purchase_price, :source, :free_balance_quantity, :balance_quantity)";
		$params = self::fetchModelsToInsert($model);
		self::executeSql($sql, $params);
		return self::last_id();
	}

	private static function fetchModelsToInsert(IProductImportModel $model): array {
		$arr = [
			'product_id' => $model->getProductId(),
			'import_id' => $model->getImportId(),
			'import_quantity' => $model->getImportQuantity(),
			'purchase_price' => $model->getPurchasePrice(),
			'source' => $model->getSource(),
			'free_balance_quantity' => $model->getFreeBalanceQuantity(),
			'balance_quantity' => $model->getBalanceQuantity(),
		];
		self::formattingModel($arr);
		return $arr;
	}

	private static function formattingModel(array &$model): void {
		$model['purchase_price'] = round($model['purchase_price'], 3);
		$model['import_quantity'] = round($model['import_quantity'], 3);
		$model['free_balance_quantity'] = round($model['free_balance_quantity'], 3);
		$model['balance_quantity'] = round($model['balance_quantity'], 3);
	}

	protected function updateProductImport(int $product_import_id, float $purchase_price): bool {
		$product_imports_schema = parent::getStorageProductImportsSchema();

		$sql = "UPDATE $product_imports_schema 
                SET `purchase_price` = $purchase_price
                WHERE $product_imports_schema.`id` = $product_import_id";

		self::executeSql($sql);
		return true;
	}

	protected function updateProductImportModel(IProductImportModel $model): void {
		$product_imports_schema = parent::getStorageProductImportsSchema();
		$product_import_id = $model->getProductImportId();
		$sql = "UPDATE $product_imports_schema 
                SET `purchase_price` = :purchase_price
                WHERE $product_imports_schema.`id` = $product_import_id";
		self::executeSql($sql, [
			'purchase_price' => $model->getPurchasePrice(),
		]);
	}

	public function getPragmaAccountId(): int {
		return $this->pragma_account_id;
	}

	static function getLinkedExportsId(array|int $productImportId): array {
		$rows = self::getLinkedExportsRows($productImportId);
		return self::groupLinkedExportsRows($rows);
	}

	private static function getLinkedExportsRows(array|int $productImportId): array {
		$productImportId = is_array($productImportId) ? $productImportId : [$productImportId];
		$id_str = implode(',', $productImportId);
		$details = self::getStorageProductExportsDetailsSchema();
		$sql = "SELECT 
       				product_export_id as export_id, 
					product_import_id as import_id
				FROM $details 
				WHERE product_import_id IN ($id_str)";
		return self::querySql($sql);
	}

	private static function groupLinkedExportsRows(array $rows): array {
		foreach ($rows as $row)
			$result[$row['import_id']][] = (int) $row['export_id'];
		return $result ?? [];
	}
}