<?php


namespace PragmaStorage;


class StorePrioritiesSchema extends \PragmaStorage\PragmaStoreDB {
	static function savePriorities(array $priorities): void {
		$values = self::createStrValues($priorities);
		if(!$values) return;
		$schema = self::getStorageExportPrioritiesSchema();
		$sql = "INSERT INTO $schema (`store_id`, `export_id`, `sort`)
				VALUES $values
				ON DUPLICATE KEY UPDATE
					`sort` = VALUES(`sort`)";
		self::executeSql($sql);
	}
	static function createStrValues(array $priorities): string {
		foreach ($priorities as $priority) {
			$model = [];
			$model[] = $priority->getStoreId();
			$model[] = $priority->getExportId();
			$model[] = $priority->getSort();
			$rows[] = '(' . implode(',', $model) . ')';
		}
		return implode(',', $rows ?? []);
	}
	static function clearPriorities(array $export_id): void {
		$strValues = implode(',', $export_id);
		if(!$strValues) return;
		$schema = self::getStorageExportPrioritiesSchema();
		$sql = "DELETE FROM $schema WHERE export_id IN ($strValues)";
		self::executeSql($sql);
	}

	static function getPriorityModels(array $export_id): array {
		$valueStr = implode(',', $export_id);
		if(!$valueStr) return [];
		$condition = "export_id IN ($valueStr)";
		$sql = self::getSql($condition);
		return self::querySql($sql);
	}

	private static function getSql(string $condition): string {
		$schema = self::getStorageExportPrioritiesSchema();
		return "SELECT store_id, export_id, sort
				FROM $schema WHERE $condition";
	}
}