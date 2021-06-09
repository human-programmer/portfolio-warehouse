<?php


namespace Services\General;

require_once __DIR__ . '/iUserToCreate.php';
require_once __DIR__ . '/../../core_crm/user/iUser.php';


abstract class iUser extends iUserToCreate implements \Services\Pragma\iUser {
	abstract function toArray(): array;
}