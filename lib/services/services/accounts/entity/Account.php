<?php


namespace Services\General;


require_once __DIR__ . '/../../../business_rules/general/account/iAccount.php';
require_once __DIR__ . '/PragmaAccount.php';
require_once __DIR__ . '/AmocrmAccount.php';
require_once __DIR__ . '/Bitrix24Account.php';

class Account extends iAccount {
	use PragmaAccount, AmocrmAccount, Bitrix24Account;

	public function __construct(array $model) {
		$this->pragmaInit($model);
		$this->amocrmInit($model);
		$this->bitrix24Init($model);
	}

	function getDomain(): string {
		if($this->getAmocrmReferer())
			return $this->getAmocrmReferer();
		return $this->getBitrix24Referer();
	}

	function toArray(): array {
		return array_merge($this->getAmocrmModel(), $this->getPragmaModel());
	}
}