<?php


namespace Services\Tests;

use Services\General\iUserToCreate;

require_once __DIR__ . '/../../../business_rules/general/user/iUserToCreate.php';


class TestCreateUser extends iUserToCreate {
	private string $surname;
	private string $middle_name;
	private string $lang;
	private string $name;
	private string $email;
	private string $phone;

	static function createSelfUnique(string|null $email = null, string|null $phone = null): TestCreateUser {
		$email = $email ?? uniqid('email') . '@test.test';
		$phone = $phone ?? rand(1000, 99999999);
		$model = [
			'surname' => uniqid('test'),
			'middle_name' => uniqid('test'),
			'lang' => 'test',
			'name' => uniqid('test'),
			'email' => $email,
			'phone' => $phone,
		];
		return new self($model);
	}

	public function __construct(array $model) {
		$this->surname = $model['surname'];
		$this->middle_name = $model['middle_name'];
		$this->lang = $model['lang'];
		$this->name = $model['name'];
		$this->email = $model['email'];
		$this->phone = $model['phone'];
	}

	function getName(): string|null {
		return $this->name;
	}

	function getSurname(): string|null {
		return $this->surname;
	}

	function getMiddleName(): string|null {
		return $this->middle_name;
	}

	function getEmail(): string|null {
		return $this->email;
	}

	function getPhone(): string|null {
		return $this->phone;
	}

	function getLang(): string|null {
		return $this->lang;
	}
}