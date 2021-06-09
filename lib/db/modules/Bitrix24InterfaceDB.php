<?php


namespace Generals;


use Configs\Configs;

trait Bitrix24InterfaceDB {

	static function getBitrix24AccountsSchema() : string {
		return '`' . self::getBitrix24DB() . '`.`account`';
	}

	static function getBitrix24ModulesSchema() : string {
		return '`' . self::getBitrix24DB() . '`.`modules`';
	}

	static function getBitrix24ModulesTokensSchema() : string {
		return '`' . self::getBitrix24DB() . '`.`modules_tokens`';
	}

	static function getBitrix24UsersSchema() : string {
		return '`' . self::getBitrix24DB() . '`.`users`';
	}

	static function getBitrix24DepartmentsSchema() : string {
		return '`' . self::getBitrix24DB() . '`.`departments`';
	}

	static function getBitrix24DB() : string {
		return Configs::getDbNames()->getBitrix24Interface();
	}
}