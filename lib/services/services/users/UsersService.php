<?php


namespace Services;


use Services\General\iUser;
use Services\General\iUserToCreate;

require_once __DIR__ . '/../../business_rules/general/user/iUsersService.php';
require_once __DIR__ . '/../Service.php';
require_once __DIR__ . '/entity/User.php';

class UsersService extends Service implements General\iUsersService {
	private static self $inst;

	static function getSelf(): UsersService {
		if(isset(self::$inst))
			return self::$inst;
		self::$inst = new self();
		return self::$inst;
	}

	function findByPhone(string $phone): iUser|null {
		$query = self::createFindByPhone($phone);
		return $this->usersGetMethod($query)[0] ?? null;
	}

	private static function createFindByPhone(string $phone): array {
		return self::createQueryWithFilter(['phone' => $phone]);
	}

	function findByEmail(string $email): iUser|null {
		$query = self::createFindByEmail($email);
		return $this->usersGetMethod($query)[0] ?? null;
	}

	private static function createFindByEmail(string $email): array {
		return self::createQueryWithFilter(['email' => $email]);
	}

	function findByPragmaId(string $id): iUser|null {
		$query = self::createFindByPragmaId($id);
		return $this->usersGetMethod($query)[0] ?? null;
	}

	private static function createFindByPragmaId(string $pragma_user_id): array {
		return self::createQueryWithFilter(['pragma_user_id' => $pragma_user_id]);
	}

	private function usersGetMethod(array $query): array {
		$result = $this->usersPost('get', $query);
		return self::createStructs($result);
	}

	private static function createStructs(array $models): array {
		foreach ($models as $model)
			$result[] = self::createStruct($model);
		return $result ?? [];
	}

	private static function createStruct(array $model): iUser {
		return new User($model);
	}

	private function usersPost(string $userMethod, array $query): array {
		$route = "/core_crm/users/$userMethod";
		return $this->servicesRequest($route, $query)['result'];
	}

	function createUser(iUserToCreate $user): iUser {
		$query = self::createQueryFromUser($user);
		$users = $this->usersPost('create', $query);
		$users = self::createStructs($users);
		return $users[0];
	}

	private static function createQueryFromUser(iUserToCreate $user): array {
		$query = self::createDefaultQuery();
		$query['data'] = self::userToArray($user);
		return $query;
	}

	private static function userToArray(iUserToCreate $user): array {
		return [
			'surname' => $user->getSurname(),
			'middle_name' => $user->getMiddleName(),
			'lang' => $user->getLang(),
			'name' => $user->getName(),
			'email' => $user->getEmail(),
			'phone' => $user->getPhone(),
		];
	}
}