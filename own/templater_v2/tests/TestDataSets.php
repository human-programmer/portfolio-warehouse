<?php


namespace TemplateEngine\Tests;


use FilesSystem\Pragma\IFile;
use FilesSystem\Pragma\Tests\FileTests;
use TemplateEngine\Pragma\DocLink;

require_once __DIR__ . '/../../../modules/files_system/tests/pragma/files/FileTests.php';


class TestDataSets {
	use FileTests;
	static string $path = __DIR__ . '/data_sets/';

	static function clearTests(): void {
	}

	static function uniqueLinkModel(): array {
		return [
			'entity_id' => rand(1, 9999999),
			'entity_type' => 'sdfsdfsdfsw',
			'template_id' => self::uniqueFile()->getFileId(),
			'file_id' => self::uniqueFile()->getFileId(),
		];
	}

	static function uniqueDocLink(): \TemplateEngine\Pragma\IDocLinkToCreate {
		$model = self::uniqueLinkModel();
		return new DocLink($model);
	}

	private static function saveRandomContent(IFile $forFile): void {
		file_put_contents($forFile->getFullUniqueName(), uniqid('asdasdasdgfghkfcbnasdasd'));
	}

	static function randomArr(): array {
		return [0,2,3,4,5,6,7];
	}

	static function getTestTemplate(): mixed {
		return file_get_contents(self::$path . 'test.docx');
	}

	static function uniqueDocLinkWithContent(): \TemplateEngine\Pragma\IDocLinkToCreate {
		$templateFile = self::fileWithDocxContent();
		$model = [
			'entity_id' => rand(1, 9999999999),
			'entity_type' => uniqid('terte'),
			'template_id' => $templateFile->getFileId(),
			'file_id' => 0,
		];
		return new DocLink($model);
	}

	static function fileWithDocxContent(): IFile {
		$file = self::uniqueFile();
		copy(self::$path . 'test.docx', $file->getFullUniqueName());
		return $file;
	}

	static function getFromJsonFile(string $fullName): mixed {
		return json_decode(file_get_contents($fullName), true);
	}
}