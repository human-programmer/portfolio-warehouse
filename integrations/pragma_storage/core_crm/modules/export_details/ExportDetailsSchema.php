<?php


namespace PragmaStorage;


class ExportDetailsSchema extends PragmaStoreDB {
	private int $pragma_account_id;

	public function __construct(int $pragma_account_id) {
		parent::__construct();

		$this->pragma_account_id = $pragma_account_id;
	}

	protected function findProductExportDetail(int $product_import_id, int $product_export_id) {
		$export_details = $this->getExportsDetailsSchema();

		$sql = $this->details_sql() . " WHERE $export_details.`product_import_id` = $product_import_id
                                        AND $export_details.`product_export_id` = $product_export_id";

		return self::query($sql)[0] ?? null;
	}

	protected function getCurrentDetailModels(int $product_export_id): array {
		$export_details = $this->getExportsDetailsSchema();

		$sql = $this->details_sql() . "WHERE $export_details.`product_export_id` = $product_export_id";

		return self::query($sql);
	}

	protected function getProductImportDetailModels(int $product_import_id): array {
		$export_details = $this->getExportsDetailsSchema();

		$sql = $this->details_sql() . "WHERE $export_details.`product_import_id` = $product_import_id";

		return self::query($sql);
	}

	private function details_sql(): string {
		$export_details = $this->getExportsDetailsSchema();

		return "SELECT 
                    $export_details.`product_export_id`,
                    $export_details.`product_import_id`,
                    $export_details.`quantity`
                FROM $export_details ";
	}

	protected function updateProductExportDetail(int $product_import_id, int $product_export_id, float $quantity): int {
		if ($quantity <= 0)
			return $this->deleteProductExportDetail($product_import_id, $product_export_id);

		$exports_details_schema = parent::getStorageProductExportsDetailsSchema();

		$quantity = round($quantity, 3);

		$sql = "INSERT INTO $exports_details_schema (`quantity`, `product_import_id`, `product_export_id`)
                VALUES ($quantity, $product_import_id, $product_export_id)
                ON DUPLICATE KEY UPDATE `quantity` = $quantity";

		self::executeSql($sql);

		return true;
	}

	protected function deleteProductExportDetail(int $product_import_id, int $export_id): bool {
		$exports_details_schema = parent::getStorageProductExportsDetailsSchema();

		$sql = "DELETE FROM $exports_details_schema 
                WHERE `product_import_id` = $product_import_id 
                  AND `product_export_id` = $export_id";

		return self::execute($sql);
	}

	protected function clearProductExportDetails(int $product_export_id): bool {
		$exports_details_schema = parent::getStorageProductExportsDetailsSchema();

		$sql = "DELETE FROM $exports_details_schema
                WHERE `product_export_id` = $product_export_id";

		return self::execute($sql);
	}

	public static function getExportsDetailsSchema(): string {
		return parent::getStorageProductExportsDetailsSchema();
	}
}