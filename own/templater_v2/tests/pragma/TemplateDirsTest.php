<?php


namespace TemplateEngine\Pragma\Tests;


use FilesSystem\Pragma\Tests\FileTests;
use TemplateEngine\Tests\TestFactory;

require_once __DIR__ . '/../TestFactory.php';
require_once __DIR__ . '/../../../../modules/files_system/tests/pragma/files/FileTests.php';

class TemplateDirsTest extends \PHPUnit\Framework\TestCase {
	use FileTests;

	function testGetTemplateDirsId(): void {
		$id1 = TestFactory::getDirs()->getTemplatesDirId();
		$id2 = TestFactory::getDirs()->getTemplatesDirId();
		$this->assertEquals($id1, $id2);
	}

	function testGetTemplateDirsId3(): void {
		$expect_dir_name = TestFactory::getNode()->getAccount()->getPragmaAccountId() . '.templates';
		$id1 = TestFactory::getDirs()->getTemplatesDirId();
		$file = TestFactory::getFiles()->getFile($id1);
		$this->assertEquals($id1, $file->getFileId());
		$this->assertEquals($expect_dir_name, $file->getTitle());
	}

	function testGetCardDirId(): void {
		$this->checkTemplateDir(rand(1, 99999999999), uniqid('testT'));
		$this->checkTemplateDir(rand(1, 99999999999), uniqid('testT'));
		$this->checkTemplateDir(rand(1, 99999999999), uniqid('testT'));
		$this->checkTemplateDir(rand(1, 99999999999), uniqid('testT'));
	}

	function checkTemplateDir(int $entity_id, string $entity_type): void {
		$expect_dir_name = "$entity_id.$entity_type";
		$id1 = TestFactory::getDirs()->getCardDirId($entity_id, $entity_type);
		$id2 = TestFactory::getDirs()->getCardDirId($entity_id, $entity_type);
		$this->assertEquals($id1, $id2);
		$file = TestFactory::getFiles()->getFile($id1);
		$this->assertEquals($id1, $file->getFileId());
		$this->assertEquals($expect_dir_name, $file->getTitle());
	}
}