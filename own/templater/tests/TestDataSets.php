<?php


namespace Templater\Tests;


use Files\iFile;
use Files\Tests\TestFiles;
use Templater\Pragma\DocLink;
use Templater\Pragma\IDocLink;

class TestDataSets {
	static string $path = __DIR__ . '/data_sets/';
	static function uniqueFile(string $name = 'ttt1.txt'): iFile {
		$file = TestFiles::uniqueFileRequest($name);
		return \Files\Tests\TestFactory::getFiles()->createFromRequest($file);
	}

	static function clearTests(): void {
		TestFiles::clear();
	}

	static function uniqueLinkModel(): array {
		return [
			'entity_id' => rand(1, 9999999),
			'entity_type' => 'sdfsdfsdfsw',
			'template_id' => self::uniqueFile()->getId(),
			'file_id' => self::uniqueFile()->getId(),
		];
	}

	static function uniqueDocLink(): IDocLink {
		$model = self::uniqueLinkModel();
		return new DocLink($model);
	}

	private static function saveRandomContent(iFile $forFile): void {
		file_put_contents($forFile->getFullUniqueName(), uniqid('asdasdasdgfghkfcbnasdasd'));
	}

	static function randomArr(): array {
		return [0,2,3,4,5,6,7];
	}

	static function getTestTemplate(): mixed {
		return file_get_contents(self::$path . 'test.docx');
	}

	static function uniqueDocLinkWithContent(): IDocLink {
		$templateFile = self::fileWithDocxContent();
		$model = [
			'entity_id' => 234234,
			'entity_type' => 'sdfsdfsdfsw',
			'template_id' => $templateFile->getId(),
			'file_id' => 0,
		];
		return new DocLink($model);
	}

	static function fileWithDocxContent(): iFile {
		$file = self::uniqueFile('test1.docx');
		copy(self::$path . 'test.docx', $file->getFullUniqueName());
		return $file;
	}

	static function getFromJsonFile(string $fullName): mixed {
		return json_decode(file_get_contents($fullName), true);
	}
}