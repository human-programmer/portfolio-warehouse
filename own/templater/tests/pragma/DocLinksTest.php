<?php


namespace Templeter\Pragma\Tests;


use Files\iFile;
use Generals\Functions\Date;
use Templater\Pragma\IDocLink;
use Templater\Pragma\Tests\TestDocLinks;
use Templater\Tests\TestDataSets;
use Templater\Tests\TestFactory;

require_once __DIR__ . '/../TestFactory.php';
require_once __DIR__ . '/modules/TestDocLinks.php';

class DocLinksTest extends \PHPUnit\Framework\TestCase {
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTest();
	}

	function testSaveFileLink(){
		$mainFileId = TestDataSets::uniqueFile()->getId();
		$docLink = TestDataSets::uniqueDocLink();
		TestDocLinks::saveFileLink($mainFileId, $docLink);
		$actualLinks = TestDocLinks::getDocLinks(TestFactory::getNode()->getAccount()->getPragmaAccountId());

		$this->assertCount(1, $actualLinks);
		$this->assertEquals($mainFileId, $actualLinks[0]['file_id']);
		$this->assertEquals($docLink->getTemplateFileId(), $actualLinks[0]['template_id']);
		$this->assertEquals($docLink->getEntityId(), $actualLinks[0]['entity_id']);
		$this->assertEquals($docLink->getEntityType(), $actualLinks[0]['entity_type']);

		$actualSec = Date::getIntTimeStamp($actualLinks[0]['date_update']);

		$this->assertTrue(abs(time() - $actualSec) < 3);
	}

	function testGetLinksOfEntity(){
		$testFile = TestDataSets::uniqueFile();
		$mainFileId = $testFile->getId();
		$docLink = TestDataSets::uniqueDocLink();
		$createdLink = TestDocLinks::saveFileLink($mainFileId, $docLink);
		$links = new TestDocLinks(TestFactory::getNode()->getAccount()->getPragmaAccountId());
		$link = $links->getLinksOfEntity($docLink->getEntityType(), $docLink->getEntityId())[0];
		$this->compareLinks($createdLink, $link);
		$this->compareFiles($testFile, $link);
	}

	private function compareLinks(IDocLink $expect, IDocLink $actual): void {
		$this->assertEquals($expect->getEntityType(), $actual->getEntityType());
		$this->assertEquals($expect->getEntityId(), $actual->getEntityId());
		$this->assertEquals($expect->getTemplateFileId(), $actual->getTemplateFileId());
		$this->assertEquals($expect->getFileId(), $actual->getFileId());
	}

	private function compareFiles(iFile $expect, iFile $actual): void {
		$this->assertEquals($expect->getFullUniqueName(), $actual->getFullUniqueName());
		$this->assertEquals($expect->getId(), $actual->getId());
		$this->assertEquals($expect->getUniqueName(), $actual->getUniqueName());
		$this->assertEquals($expect->getExternalLink(), $actual->getExternalLink());
		$this->assertEquals($expect->getFullUniqueName(), $actual->getFullUniqueName());
		$this->assertEquals($expect->getPath(), $actual->getPath());
		$this->assertEquals($expect->getExtension(), $actual->getExtension());
		$this->assertEquals($expect->getTitle(), $actual->getTitle());
		$this->assertEquals($expect->getName(), $actual->getName());
		$this->assertEquals($expect->getSize(), $actual->getSize());
	}
}