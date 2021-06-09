<?php


namespace Services\Tests;


use Files\iFile;
use Services\Amocrm\iAmoEntityParams;

require_once __DIR__ . '/TestEntityParams.php';
require_once __DIR__ . '/../../../../../modules/files/tests/TestFactory.php';

class TestDataSets {
	private static string $path = __DIR__ . '/data_sets/';
	private static array $files = [];

	static function getParams(): iAmoEntityParams {
		$entities = self::getEntities();
		$customFields = self::getCustomFields();
		$users = self::getUsers();
		return new TestEntityParams($entities, $users, $customFields);
	}

	private static function getEntities(): array {
		$lead = self::getContentFromJson('LeadsLoader.json')['body']['_embedded']['leads'][0];
		$contact = self::getContentFromJson('ContactsLoader.json')['body']['_embedded']['contacts'][0];
		$company = self::getContentFromJson('CompaniesLoader.json')['body']['_embedded']['companies'][0];
		return [$lead, $contact, $company];
	}

	private static function getCustomFields(): array {
		return [
				...self::getContentFromJson('LeadsCfLoader.json')['body']['_embedded']['custom_fields'],
				...self::getContentFromJson('ContactsCfLoader.json')['body']['_embedded']['custom_fields'],
				...self::getContentFromJson('CompaniesCfLoader.json')['body']['_embedded']['custom_fields'],
			];
	}

	private static function getUsers(): array {
		return self::getContentFromJson('UsersLoader.json')['body']['_embedded']['users'];
	}

	private static function getContent(string $fileName): mixed {
		return file_get_contents(self::$path . $fileName);
	}

	private static function getContentFromJson(string $fileName): mixed {
		return json_decode(file_get_contents(self::$path . $fileName), true);
	}

	static function getUniqueTemplateLink(): string {
		$file = self::testFile();
		return str_replace('https://smart-dev.core_crm.by', 'http://smart-dev.core_crm', $file->getExternalLink());
	}

	static function testFile(): iFile {
		copy(self::$path . 'test.docx', self::$path . 'test1.docx');
		$request = [
			'tmp_name' => self::$path . 'test1.docx',
			'name' => 'test.docx',
			'size' => '123',
		];
		$file = \Files\Tests\TestFactory::getFiles()->createFromRequest($request);
		self::$files[] = $file->getId();
		return $file;
	}

	static function clearTest(): void{
		foreach (self::$files as $id)
			\Files\Tests\TestFactory::getFiles()->delete($id);
		self::$files = [];
	}
}