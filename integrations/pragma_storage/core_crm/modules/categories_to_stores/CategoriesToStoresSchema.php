<?php


namespace PragmaStorage;


class CategoriesToStoresSchema extends PragmaStoreDB {
	function __construct(private int $account_id) {
		parent::__construct();
	}

	protected static function saveLinkRows(array $structs): void {
		$strValues = self::strValues($structs);
		if(!$strValues) return;
		$links = self::getStorageCategoriesToStoresSchema();
		$sql = "INSERT INTO $links (store_id, category_id, status)
				VALUES $strValues
				ON DUPLICATE KEY UPDATE
					store_id = VALUES(store_id),
					category_id = VALUES(category_id),
					status = VALUES(status)";
		self::executeSql($sql);
	}

	private static function strValues(array $structs): string {
		foreach ($structs as $struct)
			$arr[] = '(' . $struct->getStoreId() . ',' . $struct->getCategoryId() . ',' . $struct->getLinkStatus() . ')';
		return implode(',', $arr ?? []);
	}

	protected function getLinksRows(): array {
		$stores = self::getStorageStoresSchema();
		$links = self::getStorageCategoriesToStoresSchema();
		$sql = "SELECT store_id, category_id, status as link_status
				FROM $links
				WHERE store_id IN (SELECT id FROM $stores WHERE account_id = $this->account_id)";
		return self::querySql($sql);
	}
}