<?php


namespace PragmaStorage;


class TravelLinksSchema extends PragmaStoreDB {
	static function save(ITravelLinkStruct $travelLink): void {
		$schema = self::getStorageExportToTravelSchema();
		$sql = "INSERT INTO $schema (travel_id, product_id, send_export_id, receive_product_import_id, quantity)
				VALUES(:t_id, :p_id, :e_id, :pi_id, :quantity)
				ON DUPLICATE KEY UPDATE
					send_export_id = VALUES(send_export_id),
					receive_product_import_id = VALUES(receive_product_import_id),
					quantity = VALUES(quantity)";
		self::executeSql($sql, [
			't_id' => $travelLink->getTravelId(),
			'p_id' => $travelLink->getProductId(),
			'e_id' => $travelLink->getStartExportId(),
			'pi_id' => $travelLink->getReceiveProductImportId(),
			'quantity' => $travelLink->getQuantity()
		]);
	}

	static function getTravelsLinksRows(int $travel_id): array {
		$sql = self::sql("travel_id = $travel_id");
		return self::querySql($sql);
	}

    static function getProductsTravelLinkRow(int $travel_id, int $product_id): array {
        $sql = self::sql("travel_id = $travel_id AND product_id = $product_id");
        return self::querySql($sql)[0];
    }

	static function getExportsLinksRows(int $export_id): array {
		$schema = self::getStorageExportToTravelSchema();
		$sql = self::sql("travel_id = (SELECT travel_id FROM $schema WHERE send_export_id = $export_id)");
		return self::querySql($sql);
	}

	static function getProductsImportsLinksRows(int $product_import_id): array {
		$schema = self::getStorageExportToTravelSchema();
		$sql = self::sql("travel_id = (SELECT travel_id FROM $schema WHERE receive_product_export_id = $product_import_id)");
		return self::querySql($sql);
	}

	private static function sql(string $condition): string {
		$schema = self::getStorageExportToTravelSchema();
		return "SELECT
					travel_id,
       				send_export_id,
       				product_id,
       				receive_product_import_id,
       				quantity
				FROM $schema
				WHERE $condition";
	}
}