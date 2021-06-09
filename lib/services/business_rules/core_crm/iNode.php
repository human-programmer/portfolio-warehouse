<?php


namespace Services\Pragma;


interface iNode {
	function getPragmaUserId(): int|null;
	function getShutdownTime(): int;
	function setShutdownTime(int $time): void;
	function isOnceInstalled(): bool;
	function isUnlimited(): bool;
	function isPragmaActive(): bool;
	function checkActive(): void;

	function createInactiveApiKey(int $pragma_user_id): string;
	function checkApiKey(string $token): bool;
}