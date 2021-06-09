<?php


namespace PragmaStorage\Test;

require_once __DIR__ . '/TestUsersSchema.php';


trait UsersCreator{
	static private $testUsers = [];

	static function uniqueUserId(): int {
		$id = TestUsersSchema::createUser();
		self::$testUsers[] = $id;
		return $id;
	}

	static function clearUsers(): void {
		TestUsersSchema::deleteUsers(self::$testUsers);
		self::$testUsers = [];
	}
}