<?php


namespace Generals;


use Configs\Configs;

trait UsersDB {
	static function getUsersSchema() : string {
		return '`' . self::getUsersDb() . '`.`users`';
	}

	static function getUsersToAccountSchema() : string {
		return '`' . self::getUsersDb() . '`.`user_to_account`';
	}

	static function getDepartmentsSchema() : string {
		return '`' . self::getUsersDb() . '`.`departments`';
	}

	static function getUserToDepartmentsSchema() : string {
		return '`' . self::getUsersDb() . '`.`user_to_departments`';
	}

	static function getPermissionsSchema() : string {
		return '`' . self::getUsersDb() . '`.`permissions`';
	}

	static function getAccessesSchema() : string {
		return '`' . self::getUsersDb() . '`.`accesses`';
	}

	static private function getUsersDb() : string {
		return Configs::getDbNames()->getUsers();
	}
}