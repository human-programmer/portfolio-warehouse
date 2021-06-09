<?php


namespace Services\Amocrm;


interface iAccount {
	function getAmocrmAccountId(): int;
	function getAmocrmSubdomain(): string;
	function getAmocrmReferer(): string;
	function getAmocrmName(): string;
	function getAmocrmCountry(): string;
	function getAmocrmCreatedByUserId(): int;
	function getAmocrmCreateTime(): int;
	function isAmocrmTechnicalAccount(): bool;
}