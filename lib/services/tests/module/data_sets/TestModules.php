<?php


namespace Services\Tests;


use Services\General\iModule;
use Services\General\Module;

require_once __DIR__ . '/TestPragmaModules.php';
require_once __DIR__ . '/TestAmocrmModule.php';

class TestModules extends \Generals\CRMDB {
	use TestPragmaModules, TestAmocrmModule;

	static function getTargetAmocrmModule(string$amocrmCode, string $amocrmId, string $secreKey, string $code = null): iModule {
		$module = self::findAmocrmModule($amocrmId);
		if($module) return $module;
		$id = self::createPragmaModuleRow(10, $code);
		self::createAmocrmInterface($id, $amocrmCode, $amocrmId, $secreKey);
		return self::findAmocrmModule($amocrmId);
	}

	static function createUniqueModule(int $freePeriod = 10): iModule {
		$id = self::createPragmaModule($freePeriod);
		self::createAmocrmInterface($id);
		return self::getTestModule($id);
	}

	private static function getTestModule(int $pragma_module_id): iModule {
		$pragma = self::getPragmaModulesSchema();
		$condition = "$pragma.id = $pragma_module_id";
		$sql = self::sql($condition);
		$model = self::querySql($sql)[0];
		return self::createModuleStruct($model);
	}

	private static function createModuleStruct(array$model): iModule {
		$model['is_free'] = !$model['free_period_days'];
		return new Module($model);
	}

	static function findAmocrmModule(string $client_id): iModule|null {
		$amocrm = self::getAmocrmModulesSchema();
		$condition = "$amocrm.integration_id = '$client_id'";
		$sql = self::sql($condition);
		$model = self::querySql($sql)[0] ?? null;
		return $model ? new Module($model) : null;
	}

	private static function sql(string $condition): string {
		$pragma = self::getPragmaModulesSchema();
		$amocrm = self::getAmocrmModulesSchema();
		return "SELECT
					$pragma.id AS pragma_module_id,
					$pragma.code AS code,
					$pragma.free_period_days AS free_period_days,
					$amocrm.integration_id AS amocrm_integration_id,
					$amocrm.code AS amocrm_code
				FROM $pragma
					INNER JOIN $amocrm ON $amocrm.pragma_id = $pragma.id
				WHERE $condition";
	}
}