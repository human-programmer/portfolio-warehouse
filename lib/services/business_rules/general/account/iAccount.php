<?php


namespace Services\General;


require_once __DIR__ . '/../../core_crm/iAccount.php';
require_once __DIR__ . '/../../amocrm/iAccount.php';
require_once __DIR__ . '/../../bitrix24/iAccount.php';


abstract class iAccount implements \Services\Pragma\iAccount, \Services\Amocrm\iAccount, \Services\Bitrix24\iAccount {
	abstract function getDomain(): string;
	abstract function toArray(): array;
}