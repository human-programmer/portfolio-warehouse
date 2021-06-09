<?php


namespace PragmaStorage;


require_once __DIR__ . '/../business_rules/iCatalog.php';
require_once __DIR__ . '/catalog/ProductsCatalog.php';
require_once __DIR__ . '/catalog/ImportsCatalog.php';
require_once __DIR__ . '/catalog/ProductsImportsCatalog.php';


class Catalog extends PragmaStoreDB implements iCatalog {
	private int $pragma_account_id;

	public function __construct(int $pragma_account_id) {
		parent::__construct();

		$this->pragma_account_id = $pragma_account_id;
	}

	public function getStoreModels(): array {
		$stores = parent::getStorageStoresSchema();

		$sql = "SELECT 
                    $stores.`id`,
                    $stores.`title`,
                    $stores.`address`,
                    $stores.`deleted`
                FROM $stores
                WHERE $stores.`account_id` = $this->pragma_account_id";

		return self::query($sql);
	}

	function getCategoriesModels(array $filter = []): array {
		$categories = parent::getStorageCategoriesSchema();
        $condition = self::createStoresCatalogCondition($filter);
        $condition = $condition ? "AND $condition" : '';

		$sql = "SELECT 
                   $categories.`id`, 
                   $categories.`title` 
                FROM $categories 
                WHERE $categories.`account_id` = $this->pragma_account_id $condition";

		return self::query($sql);
	}

	private static function createStoresCatalogCondition(array $filter = []): string {
        $filterObj = new CatalogFilter($filter);
        $id = $filterObj->fetchStoreId();
        if(!count($id)) return '';
        $links = self::getStorageCategoriesToStoresSchema();
        $str_val = implode(',', $id);
        return "id IN (SELECT category_id FROM $links WHERE store_id IN($str_val))";
    }

	function getProductModels(array $filter = []): array {
		$productsCatalog = new ProductsCatalog($this->pragma_account_id, $filter);
		return $productsCatalog->getProducts();
	}

	function getImportModels(array $filter = []): array {
        $importsCatalog = new ImportsCatalog($this->pragma_account_id, $filter);
        return $importsCatalog->getImports();
	}

	function getProductImportModels(array $filter = []): array {
        $productsImportsCatalog = new ProductsImportsCatalog($this->pragma_account_id, $filter);
        return $productsImportsCatalog->getProductsImports();
	}

	function getExportModels(array $filter = []) {
//		$imports = parent::getStorageImportsSchema();
//		$stores = parent::getStorageStoresSchema();
//		$product_imports = parent::getStorageProductImportsSchema();
//		$exports = parent::getStorageProductExportsSchema();
//		$exports_details = parent::getStorageProductExportsDetailsSchema();
//		$products = parent::getStorageProductsSchema();
//
//		$sql = "SELECT
//                   $exports.`id`,
//                   $exports.`product_id`,
//                   $exports.`entity_id` AS `pragma_entity_id`,
//                   $exports.`quantity`,
//                   $exports.`selling_price`,
//                   $exports.`status_id`,
//                   $exports.`date_create`
//                FROM $exports
//					INNER JOIN $product_imports ON $product_imports.`product_id` = $exports.`product_id`
//					LEFT JOIN $imports ON $product_imports.`import_id` = $imports.`id`
//                WHERE $products.`account_id` = $this->pragma_account_id";
//
//		foreach ($filter as $field_name => $values)
//			switch ($field_name) {
//				case 'id':
//					if ($field_name === 'id')
//						$name = "$exports.`id`";
//
//				case 'store_id':
//					if ($field_name === 'store_id')
//						$name = "$stores.`id`";
//
//				case 'import_id':
//					if ($field_name === 'import_id')
//						$name = "$imports.`id`";
//
//				case 'product_id':
//					if ($field_name === 'product_id')
//						$name = "$exports.`product_id`";
//
//				case 'status_id':
//					if ($field_name === 'status_id')
//						$name = "$exports.`status_id`";
//
//					$result = self::filter_parser($name ?? '', $values);
//
//					if ($result)
//						$arr[] = $result;
//
//					break;
//				case 'date':
//					$result = self::parser_date_filter("$exports.`date_create`", $values);
//
//					if ($result)
//						$arr[] = $result;
//
//					break;
//			}
//
//		$condition = implode(' AND ', $arr ?? []) . " OR $stores.`id` IS NULL";
//
//		if ($condition)
//			$sql .= " AND $condition";
//
//		$sql .= " GROUP BY $exports.`id` " . self::get_order("$imports.`date`", $filter['order'] ?? '');
//
//		PragmaFactory::getLogWriter()->add('getExportModels', $sql);
//
//		return self::query($sql);
	}

	function getProductDeficitModels(array $filter = []): array {
		$product_imports = parent::getStorageProductImportsSchema();
		$products = parent::getStorageProductsSchema();

		$sql = "SELECT 
                   $product_imports.`id`,
                   $product_imports.`product_id`,
                   $product_imports.`import_id`,
                   SUM($product_imports.`quantity`) AS `quantity`
                FROM $products 
                    INNER JOIN $product_imports ON $product_imports.`product_id` = $products.`id`
                WHERE $products.`account_id` = $this->pragma_account_id AND $product_imports.`import_id` IS NULL";

		foreach ($filter as $field_name => $values)
			switch ($field_name) {
				case 'id':
					if ($field_name === 'id')
						$name = "$product_imports.`id`";

				case 'product_id':
					if ($field_name === 'product_id')
						$name = "$product_imports.`product_id`";

				case 'category_id':
					if ($field_name === 'category_id')
						$name = "$products.`category_id`";

					$result = self::filter_parser($name ?? '', $values);

					if ($result)
						$arr[] = $result;

					break;
			}

		$condition = implode(' AND ', $arr ?? []);

		if ($condition)
			$sql .= " AND $condition";

		$sql .= " GROUP BY $products.`id`";

		return self::query($sql);
	}

	function getPragmaEntityExportModels(int $pragma_entity_id): array {
		$exports = parent::getStorageProductExportsSchema();

		$sql = "SELECT 
                   $exports.`id`,
                   $exports.`product_id`,
                   $exports.`entity_id` AS `pragma_entity_id`,
                   $exports.`quantity`,
                   $exports.`selling_price`,
                   $exports.`status_id`, 
                   $exports.`date_create` 
                FROM $exports
                WHERE $exports.`entity_id` = $pragma_entity_id";

		$exports = self::query($sql);

		return $this->linkDetails($exports);
	}

	protected function linkDetails(array &$export_models): array {
		foreach ($export_models as $model)
			$ids[] = $model['id'];

		$details = isset($ids) ? $this->getDetails(['export_id' => $ids]) : [];

		foreach ($export_models as &$model)
			$model['export_details'] = self::filterDetails($details, $model['id']);

		return $export_models;
	}

	private function getDetails(array $filter): array {
		$exports_details = parent::getStorageProductExportsDetailsSchema();
		$imports = parent::getStorageImportsSchema();
		$stores = parent::getStorageStoresSchema();
		$product_imports = parent::getStorageProductImportsSchema();

		$sql = "SELECT 
                    $exports_details.`product_export_id` AS `export_id`,
                    $exports_details.`product_import_id`,
                    $exports_details.`quantity`,
                    $imports.`id` AS `import_id`,
                    $imports.`date` AS `import_date`,
                    $stores.`title` AS `store_name`,
                    $stores.`address` AS `store_address`
                FROM $exports_details
                    LEFT JOIN $product_imports ON $product_imports.`id` = $exports_details.`product_import_id`
                    LEFT JOIN $imports ON $imports.`id` = $product_imports.`import_id`
                    LEFT JOIN $stores ON $stores.`id` = $imports.`store_id`
                WHERE ";

		foreach ($filter as $field_name => $values)
			switch ($field_name) {
				case 'export_id':
					if ($field_name === 'export_id')
						$name = "$exports_details.`product_export_id`";

				case 'product_import_id':
					if ($field_name === 'product_import_id')
						$name = "$exports_details.`product_import_id`";

					$result = self::filter_parser($name ?? '', $values);

					if ($result)
						$arr[] = $result;

					break;
			}

		$condition = implode(' AND ', $arr ?? []);

		if (!$condition)
			throw new \Exception('Filter for export_details is missing');

		$sql .= "$condition";

		return self::query($sql);
	}

	protected static function filterDetails(array $details, int $export_id): array {
		foreach ($details as $detail)
			if ($detail['export_id'] === $export_id)
				$result[] = $detail;

		return $result ?? [];
	}

	protected static function get_order(string $field_name, string $order = ''): string {
		$order = strtoupper(trim($order));

		if ($order !== 'DESC' && $order !== 'ASC')
			$order = 'DESC';

		return " ORDER BY $field_name $order";
	}

	protected static function parser_date_filter(string $field_name, array $date): string {
		if (!isset($date['start']) && !isset($date['end']))
			return '';

		$start = parent::to_date($date['start'] ?? 0);

		$end = parent::to_date($date['end'] ?? time());

		return "$field_name BETWEEN '$start' AND '$end'";
	}

	protected static function filter_parser(string $field_name, $values): string {
		$values = is_array($values) ? $values : [$values];

		foreach ($values as $value)
			$arr[] = "$field_name = '$value'";

		$result = implode(' OR ', $arr ?? []);

		return $result ? "($result)" : '';
	}

	protected static function format(array $models): array {
		foreach ($models as &$model)
			foreach ($model as $field_name => $value)
				switch ($field_name) {
					case 'balance':
						if (!is_null($value))
							$model[$field_name] = (float)$value;
				}
		return $models;
	}

	public function getPragmaAccountId() {
		return $this->pragma_account_id;
	}

    function getUnits():array
    {
        $products_schema = $this->getStorageProductsSchema();
        $pragma_account_id = $this->getPragmaAccountId();
        $sql = "SELECT 
                    $products_schema.`unit` 
                FROM $products_schema
                WHERE account_id = $pragma_account_id GROUP BY unit";

        return self::query($sql);

    }
}