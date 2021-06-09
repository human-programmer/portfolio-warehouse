<?php


namespace PragmaStorage;


use Generals\CRMDB;

require_once __DIR__ . '/../../PragmaFactory.php';

trait DeletedEntitiesToExportsSchema {
	static function saveDeletedEntityToExportsLink (int $pragma_entity_id, int $export_id) : void {
		$schema = CRMDB::getStorageDeletedEntitiesToExportsSchema();
		$sql = "INSERT INTO $schema (`deleted_entity_id`, `export_id`)
				VALUES($pragma_entity_id, $export_id)";
		if(!self::execute($sql))
			throw new \Exception("Unknown ERROR");
	}
}