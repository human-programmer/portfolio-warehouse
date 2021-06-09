<?php


namespace Services;


use PragmaStorage\PragmaFactory;
use Services\General\iModule;


require_once __DIR__ . '/../../business_rules/general/module/iModulesService.php';
require_once __DIR__ . '/../Service.php';
require_once __DIR__ . '/../../services/modules/entity/Module.php';

class ModulesService implements General\iModulesService {
	private static self $inst;

	static function getSelf(): ModulesService {
		if(isset(self::$inst))
			return self::$inst;
		self::$inst = new self();
		return self::$inst;
	}

	function createModule(string $code): iModule {
		// TODO: Implement createModule() method.
	}

	function getPragmaModule(int $pragma_module_id): iModule {
		// TODO: Implement getPragmaModule() method.
	}

	function findAmocrmModule(string $amocrm_integration_id): iModule {
		// TODO: Implement findAmocrmModule() method.
	}

	function findBitrix24Module(string $bitrix24_client_id): iModule {
		// TODO: Implement findBitrix24Module() method.
	}

	function findModule(string $code): iModule|null {
		// TODO: Implement findModule() method.
	}

	function allowedToDeleteEntity(int $pragma_entity_id): bool {
		require_once __DIR__ . '/../../../../integrations/pragma_storage/core_crm/PragmaFactory.php';
		require_once __DIR__ . '/../../../../integrations/calculator/PragmaFactory.php';
		return
			PragmaFactory::getRemovalInspector()->allowedToDeleteEntity($pragma_entity_id) &&
			\Calculator\PragmaFactory::getRemovalInspector()->allowedToDeleteEntity($pragma_entity_id);
	}

	function allowedToDeleteField(int $pragma_field_id): bool {
		require_once __DIR__ . '/../../../../integrations/pragma_storage/core_crm/PragmaFactory.php';
		require_once __DIR__ . '/../../../../integrations/calculator/PragmaFactory.php';
		return
			PragmaFactory::getRemovalInspector()->allowedToDeleteField($pragma_field_id) &&
			\Calculator\PragmaFactory::getRemovalInspector()->allowedToDeleteField($pragma_field_id);
	}
}