<?php


namespace TemplateEngine\Amocrm\Tests;


use FilesSystem\Pragma\IFile;
use FilesSystem\Pragma\Tests\FileTests;
use TemplateEngine\Amocrm\AmocrmTemplateEngine;
use TemplateEngine\Amocrm\Factory;
use TemplateEngine\Pragma\ITemplateEngine;
use const FilesSystem\TYPE_FILE_IS_DIR;

require_once __DIR__ . '/TestFactory.php';
require_once __DIR__ . '/libs/TestAmocrmTemplateEngine.php';
require_once __DIR__ . '/../../../../modules/files_system/tests/pragma/files/FileTests.php';

class AmocrmTemplateEngineTest extends \PHPUnit\Framework\TestCase {
	use FileTests;

	/**
	 * @dataProvider engine
	 */
	function testCreateDir(ITemplateEngine $engine): void {
		$dir = Factory::getFiles()->createDir('test');
		$this->assertTrue($dir->getType() === TYPE_FILE_IS_DIR);
		$test_dir1 = $engine->createDir('dir1', $dir->getFileId());
		$this->assertEquals('dir1', $test_dir1->getTitle());
		$this->assertEquals(TYPE_FILE_IS_DIR, $test_dir1->getType());
		$this->assertEquals($dir->getFileId(), $test_dir1->getParentId());
	}

	function testCreateAndLink(){
		\TemplateEngine\Pragma\Tests\TestFactory::initTestService();
		$link = TestAmoDataSets::uniqueDocLinkWithContent();
		$params = TestAmoDataSets::uniqueAmoEntityParams();
		$docLink = new TestAmocrmTemplateEngine();
		$resultFile = $docLink->createFile($link, $params);
		$this->assertInstanceOf(IFile::class, $resultFile);
		$this->checkResultDoc($resultFile->getFullUniqueName());
	}
	private function checkResultDoc(string $name): void {
		$content = file_get_contents($name);
		$this->assertNotFalse(strpos($content, "+375(33) 333-33-33"));
		$this->assertNotFalse(strpos($content, "Номер Телефона 375296500228"));
		$this->assertNotFalse(strpos($content, "йцуйцу"));
		$this->assertNotFalse(strpos($content, "Developer AMO"));
		$this->assertNotFalse(strpos($content, "Google"));
		$this->assertNotFalse(strpos($content, "Facebook"));
		$this->assertNotFalse(strpos($content, "utm_term еуые еуые"));
		$this->assertNotFalse(strpos($content, "IP: 127.0.0.1; Desktop Chrome 84 Windows 10 Устр. Other"));
		$this->assertNotFalse(strpos($content, "375296500228"));
	}

	static function engine(): array {
		return [[new AmocrmTemplateEngine()]];
	}
}