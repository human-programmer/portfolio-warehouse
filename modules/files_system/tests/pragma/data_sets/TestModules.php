<?php


namespace FilesSystem\Pragma\Tests;


use Generals\CRMDB;
use Services\General\iModule;
use Services\General\Module;

class TestModules extends CRMDB {
	private static array $modules = [];

	static function uniqueModule(): iModule {
		$model = self::uniqueModel();
		return new Module($model);
	}

	private static function uniqueModel(): array {
		$code = uniqid('module_');
		$id = self::createPragmaModuleRow(0, $code);
		self::$modules[] = $id;
		return [
			'pragma_module_id' => $id,
			'code' => $code,
			'free_period_days' => 0,
			'is_free' => true,
		];
	}

	private static function createPragmaModuleRow(int $frePeriod, string|null $code = null): int {
		$pragma = self::getPragmaModulesSchema();
		$code = $code ?? uniqid('test');
		$sql = "INSERT INTO $pragma (code, free_period_days)
				VALUES(:code, :free)";
		$model = ['code' => $code, 'free' => $frePeriod];
		self::executeSql($sql, $model);
		return self::last_id();
	}

	static function clear(): void {
		if(!count(self::$modules)) return;
		$schema = self::getPragmaModulesSchema();
		$condition = self::createRemoveCondition();
		$sql = "DELETE FROM $schema WHERE $condition";
		self::executeSql($sql);
		self::$modules = [];
	}

	private static function createRemoveCondition(): string {
		$schema = self::getPragmaModulesSchema();
		foreach (self::$modules as $index => $id)
			$arr[] = "$schema.`id` = $id";
		return implode(' OR ', $arr);
	}
}