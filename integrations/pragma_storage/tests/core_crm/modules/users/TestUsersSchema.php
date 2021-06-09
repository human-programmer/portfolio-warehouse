<?php


namespace PragmaStorage\Test;


class TestUsersSchema extends \PragmaStorage\PragmaStoreDB {
	static function createUser(): int {
		$users = self::getUsersSchema();
		$sql = "INSERT INTO $users (`name`) VALUES('test')";
		self::executeSql($sql);
		return self::last_id();
	}

	static function deleteUsers(array $id): void {
		if(!count($id)) return;
		$users = self::getUsersSchema();
		$idStr = implode(',', $id);
		$sql = "DELETE FROM $users WHERE id IN ($idStr)";
		self::executeSql($sql);
	}
}