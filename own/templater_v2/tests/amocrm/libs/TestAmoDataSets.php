<?php


namespace TemplateEngine\Amocrm\Tests;


use Services\Amocrm\iAmoEntityParams;
use TemplateEngine\Amocrm\AmoEntityParams;
use TemplateEngine\Tests\TestDataSets;

require_once __DIR__ . '/../../TestDataSets.php';

class TestAmoDataSets extends TestDataSets {
	static string $amoPath = __DIR__ . '/../../data_sets/amocrm/';

	static function uniqueAmoEntityParams(): iAmoEntityParams {
		$model = [
			'entities' => self::amoEntities(),
			'managers' => self::amoManagers(),
			'customFields' => self::amoCustomFields(),
		];
		return new AmoEntityParams($model);
	}

	static function amoEntities(): array {
		$contact = self::getFromJsonFile(self::$amoPath . 'ContactsLoader.json')['body']['_embedded']['contacts'][0];
		$lead = self::getFromJsonFile(self::$amoPath . 'LeadsLoader.json')['body']['_embedded']['leads'][0];
		$company = self::getFromJsonFile(self::$amoPath . 'CompaniesLoader.json')['body']['_embedded']['companies'][0];
		$contact['entity_type'] = 'contacts';
		$lead['entity_type'] = 'leads';
		$company['entity_type'] = 'companies';
		return [$company, $contact, $lead];
	}

	static function amoManagers(): array {
		return self::getFromJsonFile(self::$amoPath . 'UsersLoader.json')['body']['_embedded']['users'];
	}

	static function amoCustomFields(): array {
		return [
			...self::getFromJsonFile(self::$amoPath . 'ContactsCfLoader.json')['body']['_embedded']['custom_fields'],
			...self::getFromJsonFile(self::$amoPath . 'LeadsCfLoader.json')['body']['_embedded']['custom_fields'],
			...self::getFromJsonFile(self::$amoPath . 'CompaniesCfLoader.json')['body']['_embedded']['custom_fields'],
		];
	}
}