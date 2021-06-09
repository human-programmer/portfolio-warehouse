<?php


namespace Services\Amocrm;


interface iNode {
	function setAmocrmDisable(): void;
	function isAmocrmEnable(): bool;
	function amocrmInstall(string $clientId, string $referer, string $code): void;
	function amocrmRestQuery(string $route, string $method, $params = null, array $headers = []): mixed;
}