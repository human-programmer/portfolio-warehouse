<?php


namespace Generals;


use Configs\Configs;

trait TemplaterDB {
	static function getTemplaterFileLinksSchema () : string {
		return '`' . self::getTemplaterDb() . '`.`amocrm_file_links`';
	}

	static function getTemplaterDirsSchema () : string {
		return '`' . self::getTemplaterDb() . '`.`docx_dirs`';
	}

	static function getTemplaterDb () : string {
		return Configs::getDbNames()->getModulesSettings();
	}
}