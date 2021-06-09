<?php


namespace Services\Tests;


trait TestPragmaModules {
	private static array $modules = [];

	private static function createPragmaModule(int $frePeriod = 0): int {
		$id = self::createPragmaModuleRow($frePeriod);
		self::$modules[] = $id;
		return $id;
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

	static function removeTestModules(): void {
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