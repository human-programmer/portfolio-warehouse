<?php


namespace Services\General;


require_once __DIR__ . '/../../core_crm/iModule.php';
require_once __DIR__ . '/../../amocrm/iModule.php';
require_once __DIR__ . '/../../bitrix24/iModule.php';


abstract class iModule implements \Services\Pragma\iModule, \Services\Amocrm\iModule, \Services\Bitrix24\iModule {
	abstract function toArray(): array;
}