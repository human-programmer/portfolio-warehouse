<?php


namespace Generals;


use Configs\Configs;

trait FilesDB {
	static function getCoreCrmFilesSchema() : string {
		return '`' . self::getCoreCrmFilesDb() . '`.`files`';
	}

	static function getCoreCrmIndexedFilesSchema() : string {
		return '`' . self::getCoreCrmFilesDb() . '`.`indexed_files`';
	}

	static function getCoreCrmRelationContainsSchema() : string {
		return '`' . self::getCoreCrmFilesDb() . '`.`relation_contains`';
	}

	static function getCoreCrmFilesDb () : string {
		return Configs::getDbNames()->getFiles();
	}
}