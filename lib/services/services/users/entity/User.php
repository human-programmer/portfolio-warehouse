<?php


namespace Services;


use Services\General\iUser;

require_once __DIR__ . '/../../../business_rules/general/user/iUser.php';
require_once __DIR__ . '/PragmaUser.php';
require_once __DIR__ . '/AmocrmUser.php';
require_once __DIR__ . '/Bitrix24User.php';

class User extends iUser {
	use PragmaUser, AmocrmUser, Bitrix24User;
	public function __construct(array$model) {
		$this->pragmaInit($model);
		$this->amocrmInit($model);
		$this->bitrix24init($model);
	}

	function toArray(): array {
		return $this->getPragmaModel();
	}
}