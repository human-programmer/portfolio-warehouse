<?php


namespace PragmaStorage;


use PragmaStorage\ProductImport\TravelLinksSchema;

require_once __DIR__ . '/../../PragmaFactory.php';
require_once __DIR__ . '/DeletedEntitiesToExportsSchema.php';


class ExportsSchema extends PragmaStoreDB {
	use DeletedEntitiesToExportsSchema;
	private int $pragma_account_id;

	protected function __construct(int $pragma_account_id) {
		parent::__construct();

		$this->pragma_account_id = $pragma_account_id;
	}

	protected function deleteProductExport(int $product_export_id): bool {
		$products_schema = self::getStorageProductsSchema();
		$exports_schema = parent::getStorageProductExportsSchema();

		PragmaFactory::getLogWriter()->setPrefix('DELETE EXPORTS');
		PragmaFactory::getLogWriter()->add('deleteProductExport', $product_export_id);

		$sql = "DELETE $exports_schema
				FROM $exports_schema
					INNER JOIN $products_schema ON $products_schema.`id` = $exports_schema.`product_id`
				WHERE $products_schema.`account_id` = $this->pragma_account_id AND $exports_schema.`id` = $product_export_id";

		return self::execute($sql);
	}

	protected function getEntityProductExports(int $entity_id): array {
		$exports = $this->getStorageProductExportsSchema();

		$sql = $this->sql() . "WHERE $exports.`entity_id` = $entity_id";

		return self::executeGetSql($sql);
	}

	protected function getProductsExports(int $product_id): array {
		$exports = $this->getStorageProductExportsSchema();

		$sql = $this->sql() . " WHERE $exports.`product_id` = $product_id";

		return self::executeGetSql($sql);
	}

	protected function createProductExport(int $entity_id, int $product_id, int $status_id, float $quantity, float $selling_price): int {
		if ($this->findEntityExport($entity_id, $product_id))
			throw new \Exception('This product_export already exists');
		$exports = $this->getStorageProductExportsSchema();
		$sql = "INSERT INTO $exports (`entity_id`, `product_id`, `quantity`, `selling_price`, `status_id`)
                VALUES ($entity_id, $product_id, $quantity, $selling_price, $status_id)";

		self::executeSql($sql);
		return self::last_id();
	}

	protected function createLinkedExportsRow(IExportModel $export): int {
        $exports = $this->getStorageProductExportsSchema();
        $status_id = EXPORT_STATUS_LINKED;
        $sql = "INSERT INTO $exports (`entity_id`, `product_id`, `quantity`, `selling_price`, `status_id`, `client_type`)
                VALUES (:entity_id, :product_id, :quantity, :selling_price, $status_id, :client_type)";
        self::executeSql($sql, [
            'product_id' => $export->getProductId(),
            'entity_id' => $export->getEntityId(),
            'quantity' => $export->getQuantity(),
            'selling_price' => $export->getSellingPrice(),
            'client_type' => $export->getClientType(),
        ]);
        return self::last_id();
    }

	protected function changeStatus(int $product_export_id, int $status_id): bool {
		$exports_schema = parent::getStorageProductExportsSchema();

		$sql = "UPDATE $exports_schema 
                SET `status_id` = $status_id
                WHERE `id` = $product_export_id";

		return self::execute($sql);
	}

	protected function updateProductExport(int $product_export_id, float $quantity, float $selling_price, int $status_id): int {
		$exports_schema = parent::getStorageProductExportsSchema();
		$sql = "UPDATE $exports_schema 
                SET `quantity` = $quantity, `selling_price` = $selling_price, `status_id` = $status_id
                WHERE `id` = $product_export_id";
		return self::execute($sql);
	}

	protected function getProductExportModel(int $product_export_id) {
		$exports_schema = parent::getStorageProductExportsSchema();

		$sql = $this->sql() . "WHERE $exports_schema.`id` = $product_export_id";

		return self::executeGetSql($sql)[0] ?? null;
	}

	protected function getProductExportModels(array $ids): array{
		$str_val = implode(',', $ids);
		if (!$str_val) return [];
		$sql = $this->sql() . "WHERE id IN($str_val)";
		return self::executeGetSql($sql) ?? [];
	}

	protected function findEntityExport(int $entity_id, int $product_id) {
		$exports_schema = parent::getStorageProductExportsSchema();
		$sql = $this->sql() . "WHERE $exports_schema.`entity_id` = $entity_id AND $exports_schema.`product_id` = $product_id";
		return self::executeGetSql($sql)[0] ?? null;
	}

	protected function findEntityExports(int $entity_id): array {
		$exports_schema = parent::getStorageProductExportsSchema();
		$sql = $this->sql() . "WHERE $exports_schema.`entity_id` = $entity_id";
		return self::executeGetSql($sql);
	}

	protected function getTravelExportModels(array $travel_id): array {
		$id_val = implode(',', $travel_id);
		if(!$id_val) return [];
		$links = self::getStorageExportToTravelSchema();
		$pre_sql = "SELECT send_export_id FROM $links WHERE travel_id IN ($id_val)";
		$sql = $this->sql() . "WHERE `id` IN ($pre_sql)";
		return self::executeGetSql($sql);
	}

	private function sql(): string {
		$exports_schema = parent::getStorageProductExportsSchema();

		return "SELECT 
                    $exports_schema.`id`,
                    $exports_schema.`entity_id` AS `pragma_entity_id`,
                    $exports_schema.`product_id`,
                    $exports_schema.`quantity`,
                    $exports_schema.`selling_price`,
                    $exports_schema.`client_type`,
                    $exports_schema.`date_create`,
                    $exports_schema.`status_id`
                FROM $exports_schema ";
	}

	protected function get_exports_for_update(int $product_id): array {
		$sql = 'SELECT * FROM (' . $this->for_update_sql($product_id) . ") AS alias
                WHERE `detail_quantity` != `quantity`
                OR `is_deficit`";

		return self::executeGetSql($sql);
	}

	private static function executeGetSql(string $sql): array {
		return self::querySql($sql);
	}

	private function for_update_sql(int $product_id): string {
		$exports_schema = parent::getStorageProductExportsSchema();
		$details = self::getStorageProductExportsDetailsSchema();
		$product_imports = self::getStorageProductImportsSchema();

		return "SELECT 
                    $exports_schema.`id`,
                    $exports_schema.`entity_id` AS `pragma_entity_id`,
                    $exports_schema.`product_id`,
                    $exports_schema.`quantity`,
                    $exports_schema.`client_type`,
                    $exports_schema.`date_create`,
                    SUM($details.`quantity`) AS `detail_quantity`,
                    MAX(CASE WHEN $product_imports.`import_id` IS NULL THEN 1 ELSE 0 END) AS `is_deficit`,
                    $exports_schema.`selling_price`,
                    $exports_schema.`status_id`
                FROM $exports_schema 
                    INNER JOIN $details ON $details.`product_export_id` = $exports_schema.`id`
                    INNER JOIN $product_imports ON $product_imports.`id` = $details.`product_import_id`
                WHERE $exports_schema.`product_id` = $product_id
                AND $exports_schema.`status_id` != 1
                GROUP BY $exports_schema.`id`";
	}

	static function getDeficitExportsId(array $product_id): array {
		$rows = self::getDeficitExportsRows($product_id);
		foreach ($rows as $row)
			$result[] = $row['export_id'];
		return $result ?? [];
	}

	static function getDeficitExportsRows(array $product_id): array {
		$products_str = implode(',', $product_id);
		if(!$products_str) return[];
		$source = DEFICIT_SOURCE;
		$product_imports = self::getStorageProductImportsSchema();
		$details = self::getStorageProductExportsDetailsSchema();
		$pre_sql = "SELECT id FROM $product_imports WHERE source = $source AND product_id IN ($products_str)";
		$sql = "SELECT product_export_id AS export_id FROM $details WHERE product_import_id IN ($pre_sql)";
		return self::querySql($sql);
	}
}