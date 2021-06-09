<?php


namespace Templeter\Pragma\Tests;


use PHPUnit\Framework\TestCase;
use Templater\Pragma\DocLink;
use Templater\Tests\TestDataSets;
use Templater\Tests\TestFactory;

require_once __DIR__ . '/../TestFactory.php';

class DocLinkTest extends TestCase {
	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestFactory::initTest();
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		TestFactory::clearTest();
	}

	function testCreate(){
		$model = TestDataSets::uniqueLinkModel();
		$link = new DocLink($model);
		$this->assertEquals($model['entity_id'], $link->getEntityId());
		$this->assertEquals($model['entity_type'], $link->getEntityType());
		$this->assertEquals($model['file_id'], $link->getFileId());
		$this->assertEquals($model['template_id'], $link->getTemplateFileId());
	}

	function testToArray(){
		$model = TestDataSets::uniqueLinkModel();
		$link = new DocLink($model);
		$actualModel = $link->toArray();
		$this->assertEquals($model['entity_id'], $actualModel['entity_id']);
		$this->assertEquals($model['entity_type'], $actualModel['entity_type']);
		$this->assertEquals($model['file_id'], $actualModel['file_id']);
		$this->assertEquals($model['template_id'], $actualModel['template_id']);
	}
}