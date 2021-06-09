<?php


namespace Services\Pragma;


interface iAccount {
	function getPragmaAccountId(): int;
	function getPragmaTimeCreate(): int;
	function getCrmName(): string;
}