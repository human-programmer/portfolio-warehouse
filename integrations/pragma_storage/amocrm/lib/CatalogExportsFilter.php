<?php


namespace PragmaStorage;


use Generals\CRMDB;
use Generals\Functions\Date;

class CatalogExportsFilter {
	private array $model;
	private int $pragma_account_id;
	private string $data_create_condition;
	private string $order_condition;
	private string $group_condition;

	public function __construct(int $pragma_account_id, array $model) {
		$this->pragma_account_id = $pragma_account_id;
		$this->model = self::formattingModel($model);
		$this->data_create_condition = self::dateCreateFormatting($model['date']);
		$this->order_condition = self::formattingOrder($model);
		$this->group_condition = $this->formattingGroupBy($model);
	}

	function getSql(): string {
		$imports = CRMDB::getStorageImportsSchema();
		$stores = CRMDB::getStorageStoresSchema();
		$product_imports = CRMDB::getStorageProductImportsSchema();
		$exports = CRMDB::getStorageProductExportsSchema();
		$exports_details = CRMDB::getStorageProductExportsDetailsSchema();
		$products = CRMDB::getStorageProductsSchema();
		$entity_interface = CRMDB::getAmocrmEntitiesSchema();
		$entities = CRMDB::getEntitiesSchema();
		$entities_to_user = CRMDB::getEntitiesToUserSchema();

		$condition = $this->getCondition();

		$quantity = self::sum("$exports.`quantity`", 'quantity');
		$selling_price = self::sum("$exports.`selling_price`", 'selling_price');
		$purchase_price = self::sum("$product_imports.`purchase_price`", 'purchase_price');

		$preSql = "SELECT 
                   $exports.`id`,
                   $exports.`product_id`,
                   $entities.`deleted` AS `entity_deleted`,
                   $entities.`id` AS `pragma_entity_id`,
                   $entity_interface.`entity_id` AS `amocrm_entity_id`,
                   $quantity,
                   $selling_price,
                   $purchase_price,
                   $exports.`status_id`,
                   $exports.`date_create`,
                   $entities_to_user.`user_id`,
                   $imports.`id` AS `import_id`,
                   $stores.`id` AS `store_id`
                FROM $exports
                    INNER JOIN $products ON $products.`id` = $exports.`product_id`
                    INNER JOIN $entities ON $exports.`entity_id` = $entities.`id`
                    RIGHT JOIN $entities_to_user ON $entities_to_user.`entity_id` = $entities.`id`
					RIGHT JOIN $entity_interface ON $entity_interface.`pragma_entity_id` = $exports.`entity_id`
					RIGHT JOIN $exports_details ON $exports_details.`product_export_id` = $exports.`id`
					RIGHT JOIN $product_imports ON $exports_details.`product_import_id` = $product_imports.`id`
					RIGHT JOIN $imports ON $product_imports.`import_id` = $imports.`id`
					RIGHT JOIN $stores ON $stores.`id` = $imports.`store_id`
                WHERE $condition";

		$sql = "SELECT
					id,
					product_id,
					status_id,
					date_create,
					user_id,
					import_id,
					store_id,
					entity_deleted,
					pragma_entity_id,
					amocrm_entity_id,
       				SUM(quantity) AS `total_quantity`,
       				SUM(selling_price * quantity) AS `total_selling_price`,
       				SUM(purchase_price * quantity) AS `total_purchase_price`
				FROM ($preSql)  AS `alias`
				WHERE 1";
		$this->addGroupBy($sql);
		return $sql;
	}

	static private function sum(string $fieldName, string $alias): string {
		$field = self::isNull($fieldName);
		return "SUM($field) AS `$alias`";
	}

	static private function isNull(string $field): string {
		return "CASE WHEN $field IS NULL THEN 0 ELSE $field END";
	}

	private function getCondition(): string {
		$conditions = [];
		$this->addAccountId($conditions);
		$this->addDateCreate($conditions);

		foreach ($this->model as $key => $value) {
			$condition = $this->getFieldCondition($key, $value);
			if($condition) $conditions[] = $condition;
		}

		$condition = implode(' AND ', $conditions ?? []);
		$product_imports = CRMDB::getStorageProductImportsSchema();
		$condition .= " GROUP BY $product_imports.`id`";
		$this->addOrder($condition);
		return $condition;
	}

	private function addAccountId(array &$arr): void {
		$arr[] = CRMDB::getStorageProductsSchema() . ".`account_id` = $this->pragma_account_id";
	}

	private function addDateCreate(array &$arr): void {
		if($this->data_create_condition)
			$arr[] = $this->data_create_condition;
	}

	private function getFieldCondition(string $key, array $values): string {
		foreach ($values as $value)
			$arr[] = $this->getValueCondition($key, $value);
		isset($arr) && $this->addNullValues($arr, $key);
		return implode(' OR ', $arr ?? []);
	}

	private function addNullValues(array &$arr, string $key): void {
		switch ($key) {
			case 'store_id':
			case 'import_id':
				$arr[] = $this->getFieldName($key) . " IS NULL";
		}
	}

	private function addGroupBy(string &$sql): void {
		$sql .= " $this->group_condition";
	}

	protected function addOrder(string &$sql): void {
		$sql .= " $this->order_condition";
	}

	private function getValueCondition(string $key, string|int $value): string {
		return $this->getFieldName($key) . " = '$value'";
	}

	private function getFieldName(string $key): string {
		return $this->getSchemaName($key) . ".`$key`";
	}

	private function getSchemaName(string $key): string {
		switch ($key) {
			case 'id':
				return CRMDB::getStorageProductExportsSchema();
			case 'store_id':
				return CRMDB::getStorageStoresSchema();
			case 'import_id':
				return CRMDB::getStorageImportsSchema();
			case 'product_id':
				return CRMDB::getStorageProductsSchema();
			case 'status_id':
				return CRMDB::getStorageProductExportsSchema();
			case 'user_id':
				return CRMDB::getEntitiesToUserSchema();
			default:
				throw new \Exception("Unknown field name '$key'");
		}
	}

	private static function formattingModel(array $model): array {
		$result['id'] = self::asUniquesInt($model['id']);
		$result['store_id'] = self::asUniquesInt($model['store_id']);
		$result['import_id'] = self::asUniquesInt($model['import_id']);
		$result['product_id'] = self::asUniquesInt($model['product_id']);
		$result['status_id'] = self::asUniquesInt($model['status_id']);
		$result['user_id'] = self::asUniquesInt($model['user_id']);
		return $result;
	}

	protected static function dateCreateFormatting(mixed $date): string {
		if (!is_array($date) || (!isset($date['start']) && !isset($date['end'])))
			return '';

		$start = Date::getStringTimeStamp($date['start'] ?? time());
		$end = Date::getStringTimeStamp($date['end'] ?? time());
		$field_name = CRMDB::getStorageProductExportsSchema() . '`date_create`';

		return "$field_name BETWEEN '$start' AND '$end'";
	}

	private static function asUniquesInt(mixed $numbers): array {
		$numbers = is_array($numbers) ? $numbers : [$numbers];
		foreach ($numbers as $number) {
			$val = (int) $number;
			if($val) $result[] = $val;
		}
		return $result ?? [];
	}

	private static function formattingOrder(array $model): string {
		$field_name = CRMDB::getStorageProductExportsSchema() . ".`date_create`";
		$order = $model['order'];
		$order = strtoupper(trim($order));

		if ($order !== 'DESC' && $order !== 'ASC')
			$order = 'DESC';

		return " ORDER BY $field_name $order";
	}

	private function formattingGroupBy(array $model): string {
		$key = $model['group_by'] ?? 'id';
		return "GROUP BY $key";
	}
}