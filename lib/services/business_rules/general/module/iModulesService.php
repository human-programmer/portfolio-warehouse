<?php


namespace Services\General;


interface iModulesService {
	static function getSelf(): iModulesService;

	function createModule(string $code): iModule;
	function getPragmaModule(int $pragma_module_id): iModule;
	function findModule(string $code): iModule|null;
	function findAmocrmModule(string $amocrm_integration_id): iModule|null;
	function findBitrix24Module(string $bitrix24_client_id): iModule|null;

	function allowedToDeleteEntity(int $pragma_entity_id): bool;
	function allowedToDeleteField(int $pragma_field_id): bool;
}