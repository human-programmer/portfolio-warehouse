<?php


namespace Services\Tests;


trait TestAmocrmModule {
	private static function createAmocrmInterface(int $pragma_module_id, string$amocrmCode = null, string $amocrm_id = null, string$secreKey = null): void {
		$amocrm = self::getAmocrmModulesSchema();
		$sql = "INSERT INTO $amocrm (`pragma_id`, `code`, `integration_id`, `secret_key`)
				VALUES(:pragma_id, :code, :id, :secret)";
		$model = [
			'pragma_id' => $pragma_module_id,
			'code' => $amocrmCode ?? uniqid('code'),
			'id' => $amocrm_id ?? uniqid('id'),
			'secret' => $secreKey ?? uniqid('secret')
		];
		self::executeSql($sql, $model);
	}
}