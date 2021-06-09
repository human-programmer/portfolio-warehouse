<?php


namespace Services\Pragma;


interface iModule {
	function getPragmaModuleId(): int;

	function getFreePeriodDays(): int;
	function setFreePeriodDays(int $days): void;

	function getCode(): string;
	function setCode(string $code): void;

	function setFree(): void;
	function isFree(): bool;
}