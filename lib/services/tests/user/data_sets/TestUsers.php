<?php


namespace Services\Tests;


use Services\General\iUser;
use Services\User;

require_once __DIR__ . '/../../../services/users/entity/User.php';
require_once __DIR__ . '/TestCreateUser.php';

class TestUsers extends \Generals\CRMDB {
	private static array $testUsers = [];

	static function createUniqueUsers(): array {
		return [
			self::createUniqueUser(),
			self::createUniqueUser(),
			self::createUniqueUser(),
			self::createUniqueUser(),
		];
	}

	static function createUniqueUser(): iUser {
		$id = self::createUniqueUserRow();
		return self::getUser($id);
	}

	private static function createUniqueUserRow(): int {
		$pragma = self::getUsersSchema();
		$sql = "INSERT INTO $pragma (name, surname, middle_name, email, confirm_email, phone, lang)
				VALUES(:name, :surname, :middle_name, :email, :confirm_email, :phone, :lang)";
		$model = [
			'name' => uniqid('name'),
			'surname' => uniqid('surname'),
			'middle_name' => uniqid('middle_name'),
			'email' => uniqid('email') . '@test.test',
			'confirm_email' => rand(0, 1),
			'phone' => rand(10000, 999999999),
			'lang' => 'qw',
		];
		self::executeSql($sql, $model);
		$id = self::last_id();
		self::$testUsers[] = $id;
		return $id;
	}

	private static function getUser(int $id): iUser {
		$model = self::getUserModel($id);
		return new User($model);
	}

	private static function getUserModel(int $id): array {
		$pragma = self::getUsersSchema();
		$condition = "$pragma.id = $id";
		$sql = self::sql($condition);
		return self::querySql($sql)[0];
	}

	private static function sql(string $condition): string {
		$pragma = self::getUsersSchema();
		return "SELECT
					$pragma.surname AS surname,
					$pragma.middle_name AS middle_name,
					$pragma.lang AS lang,
					$pragma.name AS name,
					$pragma.email AS email,
					$pragma.phone AS phone,
					$pragma.id AS pragma_user_id,
					$pragma.confirm_email AS confirm_email
				FROM $pragma 
				WHERE $condition";
	}

	static function removeTestUsers(array$testUsers = null): void {
		$testUsers = $testUsers ?? self::$testUsers;
		if(!count($testUsers)) return;
		$pragma = self::getUsersSchema();
		foreach ($testUsers as $userId)
			$arr[] = "$pragma.id = $userId";
		$condition = implode(' OR ', $arr);
		$sql = "DELETE FROM $pragma WHERE $condition";
		self::executeSql($sql);
		self::$testUsers = [];
	}
}