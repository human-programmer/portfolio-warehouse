<?php


namespace FilesSystem\Pragma;


use Generals\CRMDB;

class RelationContainsSchema extends CRMDB {
	static function getFileParents(int $file_id): array {
		$relations = self::getPragmaRelationContainsSchema();
		$sql = "SELECT parent_id, child_id
				FROM $relations
				WHERE child_id = $file_id";
		return self::querySql($sql);
	}

	static function saveParentRelations(int $child_id, array $parents): void {
		self::clearParentsRelations($child_id);
		self::addParentRelation($child_id, $parents);
	}

	private static function addParentRelation(int $child_id, array $parents): void {
		if(!count($parents)) return;
		$strValues = self::getValueStr($child_id, $parents);
		$relations = self::getPragmaRelationContainsSchema();
		$sql = "INSERT INTO $relations (parent_id, child_id)
				VALUES $strValues
				ON DUPLICATE KEY UPDATE
					parent_id = VALUES(parent_id),
					child_id = VALUES(child_id)";
		self::executeSql($sql);
	}

	private static function getValueStr(int $child_id, array $parents): string {
		$res = [];
		foreach ($parents as $parent_id)
			$res[] = "($parent_id, $child_id)";
		return implode(',', $res);
	}

	private static function clearParentsRelations(int $child_id): void {
		$relations = self::getPragmaRelationContainsSchema();
		$sql = "DELETE FROM $relations WHERE child_id = $child_id";
		self::executeSql($sql);
	}
}