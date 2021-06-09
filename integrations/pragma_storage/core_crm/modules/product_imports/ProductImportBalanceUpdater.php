<?php


namespace PragmaStorage;


trait ProductImportBalanceUpdater {
	static function updateBalance(int $productImportId): array {
		$imported = (float) self::getImportedQuantity($productImportId);
		return self::updateBalanceWithImportQuantity($productImportId, $imported);
	}

	static function updateBalanceWithImportQuantity(int $productImportId, float $imported): array {
		$exportedAndReserved = (float) self::getExportAndReservedQuantity($productImportId);
		$exported = (float) self::getExportedQuantity($productImportId);
		$free_balance = $imported - $exportedAndReserved;
		$balance = $imported - $exported;
		self::saveAllBalance($productImportId, $imported, $free_balance, $balance);
		return [
			'import_quantity' => $imported,
			'free_balance_quantity' => $free_balance,
			'balance_quantity' => $balance,
		];
	}

	private static function getImportedQuantity(int $productImportId): float {
		$product_imports = self::getStorageProductImportsSchema();
		$sql = "SELECT 
                   $product_imports.`quantity` AS `import_quantity`
                FROM $product_imports
                WHERE $product_imports.id = $productImportId";
		return self::querySql($sql)[0]['import_quantity'] ?? 0.0;
	}

	static function saveAllBalance(int $productImportId, float $imported, float $free_balance, float $balance): void {
		$imports = self::getStorageProductImportsSchema();
		$sql = "UPDATE $imports 
				SET `free_balance` = $free_balance,
					`balance` = $balance,
					`quantity` = $imported
				WHERE `id` = $productImportId";
		self::executeSql($sql);
	}

	static function getExportAndReservedQuantity(int $productImportId): float {
		$details = self::getStorageProductExportsDetailsSchema();
		$sql = "SELECT
				SUM(quantity) as quantity
			   FROM $details
			   WHERE $details.`product_import_id` = $productImportId
			   GROUP BY $details.`product_import_id`";
		return self::querySql($sql)[0]['quantity'] ?? 0;
	}

	static function getExportedQuantity(int $productImportId): float {
		$details = self::getStorageProductExportsDetailsSchema();
		$exports = self::getStorageProductExportsSchema();
		$status = EXPORT_STATUS_EXPORTED;
		$sql = "SELECT
				SUM($details.quantity) as quantity
			   FROM $details
					INNER JOIN $exports ON $exports.id = $details.product_export_id
			   WHERE $exports.status_id = $status AND $details.`product_import_id` = $productImportId
			   GROUP BY $details.`product_import_id`";
		return self::querySql($sql)[0]['quantity'] ?? 0;
	}
}