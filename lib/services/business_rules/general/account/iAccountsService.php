<?php


namespace Services\General;


interface iAccountsService {
	static function getSelf(): iAccountsService;

	function getAccount(int $pragma_account_id): iAccount;
	function findAmocrmById(int $amocrm_account_id): iAccount|null;
	function findAmocrmBySubdomain(string $subdomain): iAccount|null;
	function findAmocrmByReferer(string $referer): iAccount|null;
	function findBitrix24ByMemberId(string $member_id): iAccount|null;
}