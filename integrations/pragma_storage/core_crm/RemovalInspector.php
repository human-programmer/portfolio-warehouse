<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../pragmacrm/core_crm/RemovalInspector.php';

class RemovalInspector extends \PragmaCRM\RemovalInspector {

	function allowedToDeleteEntity(int $pragma_entity_id): bool {
		$exported_exports = self::getClosedExportsByEntityId($pragma_entity_id);
		return !count($exported_exports);
	}

	function allowedToDeleteField(int $pragma_field_id): bool {
		return true;
	}

	static private function getClosedExportsByEntityId (int $pragma_entity_id) : array {
		$schema = self::getStorageProductExportsSchema();
		$sql = "SELECT 
					$schema.`entity_id` AS `pragma_entity_id`,
					$schema.`status_id` AS `export_status_id`
				FROM $schema
				WHERE $schema.`entity_id` = $pragma_entity_id 
				  AND $schema.`status_id` = 3";
		$answer = self::query($sql);
		if($answer === false)
			throw new \Exception('Unknown Error');
		return $answer;
	}
}