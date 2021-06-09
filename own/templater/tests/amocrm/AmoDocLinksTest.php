<?php


namespace Templater\Amocm\Tests;


use Files\iFile;
use Templater\Amocrm\AmoDocLinks;
use Templater\Tests\TestFactory;

require_once __DIR__ . '/TestFactory.php';
require_once __DIR__ . '/libs/TestAmoDocLinks.php';

class AmoDocLinksTest extends \PHPUnit\Framework\TestCase {
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTest();
	}

	function testCreateAndLink(){
		$link = TestAmoDataSets::uniqueDocLinkWithContent();
		$params = TestAmoDataSets::uniqueAmoEntityParams();
		$docLink = new TestAmoDocLinks(TestFactory::getNode()->getAccount()->getPragmaAccountId());
		$resultFile = $docLink->createAndLink($link, $params);
		$this->assertInstanceOf(iFile::class, $resultFile);
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
}