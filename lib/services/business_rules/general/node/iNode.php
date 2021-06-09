<?php


namespace Services\General;


require_once __DIR__ . '/../../core_crm/iNode.php';
require_once __DIR__ . '/../../amocrm/iNode.php';
require_once __DIR__ . '/../../bitrix24/iNode.php';


abstract class iNode implements \Services\Pragma\iNode, \Services\Amocrm\iNode, \Services\Bitrix24\iNode {
	abstract function getAccount(): iAccount;
	abstract function getModule(): iModule;
	abstract function getUser(): iUser|null;
	abstract function toArray(): array;
	abstract function isActive(): bool;
	abstract function getLoaders(): array;
}