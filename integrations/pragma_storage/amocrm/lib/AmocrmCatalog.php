<?php


namespace PragmaStorage;


require_once __DIR__ . '/../../core_crm/modules/Catalog.php';
require_once __DIR__ . '/business_rules/iAmocrmCatalog.php';


class AmocrmCatalog extends Catalog implements iAmocrmCatalog {
	public function __construct(int $pragma_account_id) {
		parent::__construct($pragma_account_id);
	}

	function getEntityExportModels(string $entity_type, string $entity_id): array {
		$pragma_entity_id = Factory::getAmocrmInterface()->getPragmaEntityId($entity_type, $entity_id);

		$exports = $this->getPragmaEntityExportModels($pragma_entity_id);

		foreach ($exports as &$export) {
			$export['entity_id'] = $entity_id;
			$export['entity_type'] = $entity_type;
		}

		return $exports;
	}

	function getExportModels(array $filter = []) {
		$imports = self::getStorageImportsSchema();
		$stores = self::getStorageStoresSchema();
		$product_imports = self::getStorageProductImportsSchema();
		$exports = self::getStorageProductExportsSchema();
		$exports_details = self::getStorageProductExportsDetailsSchema();
		$products = self::getStorageProductsSchema();
		$entity_interface = parent::getAmocrmEntitiesSchema();
		$deleted_entities = parent::getStorageDeletedEntitiesToExportsSchema();

		$pragma_entity_id = "CASE WHEN $exports.`entity_id` IS NULL THEN $deleted_entities.`deleted_entity_id` ELSE $exports.`entity_id` END";
		$entity_deleted = "CASE WHEN $exports.`entity_id` IS NULL THEN true ELSE false END";

		$sql = "SELECT 
                   $exports.`id`,
                   $exports.`product_id`,
                   $entity_deleted AS `entity_deleted`,
                   $pragma_entity_id AS `pragma_entity_id`,
                   $entity_interface.`entity_id` AS `entity_id`,
                   $exports.`quantity`,
                   $exports.`selling_price`,
                   $exports.`status_id`, 
                   $exports.`date_create` 
                FROM $exports 
                    INNER JOIN $products ON $products.`id` = $exports.`product_id`
					LEFT JOIN $entity_interface ON $entity_interface.`pragma_entity_id` = $exports.`entity_id`
					LEFT JOIN $exports_details ON $exports_details.`product_export_id` = $exports.`id`
					LEFT JOIN $product_imports ON $exports_details.`product_import_id` = $product_imports.`id`
					LEFT JOIN $imports ON $product_imports.`import_id` = $imports.`id`
					LEFT JOIN $stores ON $stores.`id` = $imports.`store_id`
					LEFT JOIN $deleted_entities ON $deleted_entities.`export_id` = $exports.`id`
                WHERE $products.`account_id` = " . $this->getPragmaAccountId();

		foreach ($filter as $field_name => $values) {
			$isNull = '';
			switch ($field_name) {
				case 'id':
					if ($field_name === 'id')
						$name = "$exports.`id`";

				case 'store_id':
					if ($field_name === 'store_id') {
						$name = "$stores.`id`";
						$isNull = "$stores.`id` IS NULL";
					}

				case 'import_id':
					if ($field_name === 'import_id') {
						$name = "$imports.`id`";
						$isNull = "$imports.`id` IS NULL";
					}

				case 'product_id':
					if ($field_name === 'product_id')
						$name = "$exports.`product_id`";

				case 'status_id':
					if ($field_name === 'status_id')
						$name = "$exports.`status_id`";

					$result = self::filter_parser($name ?? '', $values);

					if ($result) {
						$result = "($result) " . ($isNull ? " OR $isNull" : "");
						$arr[] = "($result)";
					}

					break;
				case 'date':
					$result = self::parser_date_filter("$exports.`date_create`", $values);

					if ($result)
						$arr[] = $result;

					break;
			}
		}

		$condition = implode(' AND ', $arr ?? []);

		if ($condition)
			$sql .= " AND $condition";
		$sql .= " GROUP BY $exports.`id` " . self::get_order("$imports.`date`", $filter['order'] ?? '');
		return self::query($sql);
	}
}