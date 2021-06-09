<?php


namespace FilesSystem\Pragma\Tests;


use FilesSystem\Pragma\File;
use FilesSystem\Pragma\IFile;

require_once __DIR__ . '/../../../pragma/components/files/File.php';
require_once __DIR__ . '/FileTests.php';

class FileTest extends \PHPUnit\Framework\TestCase {
	use FileTests;

	/**
	 * @dataProvider model
	 */
	function testCreate(array $model): void {
		$file = new File($model);
		$this->assertEquals($model['token'], $file->getToken());
		$this->assertEquals($model['account_id'], $file->getAccountId());
		$this->assertEquals($model['module_id'], $file->getModuleId());
		$this->assertEquals($model['day_dir'], $file->getDayDir());
		$this->assertEquals($model['id'], $file->getFileId());
		$this->assertEquals($model['extension'], $file->getExtension());
		$this->assertEquals($model['title'], $file->getTitle());
		$this->assertEquals($model['size'], $file->getSize());
		$this->assertEquals($model['type'], $file->getType());
		$this->assertEquals($model['parent_id'], $file->getParentId());

		$this->assertEquals($model['id'] . '.' . $model['extension'], $file->getUniqueName());
		$this->assertEquals('file=' . $model['id'] . "&token=" . $model['token'], $file->getParams());

		$this->checkContent($file);
		$this->checkExternalModel($file);
	}

	private function checkContent(IFile $file): void {
		$content = 'sdfjglsjdfgliudsfgjsdfg';
		if(!is_dir($file->getSystemPath()))
			mkdir($file->getSystemPath(), 0777, true);
		file_put_contents($file->getFullUniqueName(), $content);
		$this->assertEquals($content, $file->getContent());
	}

	private function checkExternalModel(IFile $file): void {
		$actual = $file->getExternalModel();
		$this->assertEquals($file->getFileId(), $actual['id']);
		$this->assertEquals($file->getParentId(), $actual['parent_id']);
		$this->assertEquals($file->getType(), $actual['type']);
		$this->assertEquals($file->getExtension(), $actual['extension']);
		$this->assertEquals($file->getTitle(), $actual['title']);
		$this->assertEquals($file->getSize(), $actual['size']);
		$this->assertEquals($file->getUniqueName(), $actual['unique_name']);
		$this->assertEquals($file->getExternalLink(), $actual['link']);
		$this->assertEquals($file->getToken(), $actual['token']);
	}

	static function model(): array {
		return [[[
			'token' => 'tekendsffgdtest',
			'account_id' => 1,
			'module_id' => 3,
			'date_create' => '2021-05-28 16:05:57',
			'day_dir' => '2021.05.28',
			'id' => 5,
			'extension' => 'json',
			'title' => 'название тест',
			'size' => 6778,
			'type' => 6,
			'parent_id' => 11,
		]]];
	}
}