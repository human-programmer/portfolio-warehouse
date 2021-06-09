<?php


namespace Services\General;


interface iNodesService {
	static function getSelf(): iNodesService;

	function findPragmaNode(string $module_code, int $pragma_account_id): iNode|null;

	function createInactiveApiKey(array $query): string;
	function checkApiKey(array $query): bool;

	function findAmocrmNode(string $amocrm_client_id, string $amocrm_subdomain): iNode|null;
	function findAmocrmReferer(string $pragma_module_code, string $referer): iNode|null;
	function findAmocrmNodeCode(string $pragma_module_code, string $amocrm_subdomain): iNode|null;
	function findAmocrmNodeAccId(string $pragma_module_code, int $amocrm_account_id): iNode|null;

	function amocrmInstall(string $client_id, string $referer, string $code): iNode;
	function amocrmRestQuery(array $query): mixed;

	function findBitrix24Node(string $pragma_module_code, string $member_id): iNode|null;
	function getNodesOfAccount(int $pragma_account_id): array;
}