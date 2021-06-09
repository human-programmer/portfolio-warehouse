<?php


namespace Services;


trait PragmaUser {
	private string $surname;
	private string $middle_name;
	private string $lang;
	private string $name;
	private string $email;
	private string $phone;
	private int $pragma_user_id;
	private bool $confirm_email;

	private function pragmaInit(array $model): void {
		$this->surname = $model['surname'] ?? '';
		$this->middle_name = $model['middle_name'] ?? '';
		$this->lang = $model['lang'] ?? '';
		$this->name = $model['name'] ?? '';
		$this->email = $model['email'] ?? '';
		$this->phone = $model['phone'] ?? '';
		$this->pragma_user_id = $model['pragma_user_id'];
		$this->confirm_email = $model['confirm_email'];
	}

	function getPragmaUserId(): int {
		return $this->pragma_user_id;
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

	function isConfirmEmail(): bool{
		return $this->confirm_email;
	}

	private function getPragmaModel(): array {
		return [
			'surname' => $this->getSurname(),
			'middle_name' => $this->getMiddleName(),
			'lang' => $this->getLang(),
			'name' => $this->getName(),
			'email' => $this->getEmail(),
			'phone' => $this->getPhone(),
			'pragma_user_id' => $this->getPragmaUserId(),
			'confirm_email' => $this->isConfirmEmail(),
		];
	}
}