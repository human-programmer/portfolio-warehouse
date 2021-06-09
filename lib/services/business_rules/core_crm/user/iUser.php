<?php


namespace Services\Pragma;

require_once __DIR__ . '/iUserToCreate.php';

interface iUser extends iUserToCreate {
	function getPragmaUserId(): int;
	function isConfirmEmail(): bool;
}