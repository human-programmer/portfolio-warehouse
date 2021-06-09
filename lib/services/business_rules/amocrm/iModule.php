<?php


namespace Services\Amocrm;


interface iModule {
	function setAmocrmSecretKey(string $key): void;
	function getAmocrmIntegrationId(): string;
	function setAmocrmIntegreationId(string $id): void;
	function getAmocrmCode(): string;
	function setAmocrmCode(string $code): void;
}