<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImport;
use PragmaStorage\ImportsBuffer;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class ImportsBufferTest extends \PHPUnit\Framework\TestCase {
	use ImportsCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		self::clearImports();
		self::clearStores();
	}

	/**
	 * @dataProvider bufferProvider
	 */
	function testAddInBuffer(TestImportsBuffer $buffer): void {
		$this->assertCount(0, $buffer->getAllImportsFromBuffer());

		$import1 = self::getUniqueImport();
		$buffer->addInBufferTest($import1);
		$this->assertCount(1, $buffer->getAllImportsFromBuffer());

		$import2 = self::getUniqueImport();
		$buffer->addInBufferTest($import2);
		$this->assertCount(2, $buffer->getAllImportsFromBuffer());

		$import3 = self::getUniqueImport();
		$buffer->addInBufferTest($import3);
		$this->assertCount(3, $buffer->getAllImportsFromBuffer());
	}

	/**
	 * @dataProvider bufferProvider
	 */
	function testDeleteFromBuffer(TestImportsBuffer $buffer): void {
		$this->assertCount(0, $buffer->getAllImportsFromBuffer());

		$import1 = self::getUniqueImport();
		$buffer->addInBufferTest($import1);
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());
		$import2 = self::getUniqueImport();
		$buffer->addInBufferTest($import2);
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());

		$this->assertCount(6, $buffer->getAllImportsFromBuffer());

		$buffer->deleteFromBufferTest($import2->getImportId());
		$this->assertCount(5, $buffer->getAllImportsFromBuffer());

		$buffer->deleteFromBufferTest($import1->getImportId());
		$this->assertCount(4, $buffer->getAllImportsFromBuffer());

		$buffer->deleteFromBufferTest($import1->getImportId());
		$this->assertCount(4, $buffer->getAllImportsFromBuffer());
	}

	/**
	 * @dataProvider bufferProvider
	 */
	function testFindInBuffer(TestImportsBuffer $buffer): void {
		$import1 = self::getUniqueImport();
		$buffer->addInBufferTest($import1);
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());
		$import2 = self::getUniqueImport();
		$this->assertNull($buffer->findInBufferTest($import2->getImportId()));
		$buffer->addInBufferTest($import2);
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());

		$this->assertEquals($import1->getImportId(), $buffer->findInBufferTest($import1->getImportId())->getImportId());
		$this->assertEquals($import2->getImportId(), $buffer->findInBufferTest($import2->getImportId())->getImportId());
	}

	/**
	 * @dataProvider bufferProvider
	 */
	function testFindInBufferDeficit(TestImportsBuffer $buffer): void {
		$store = self::getUniqueStore();
		$import1 = self::getUniqueDeficitImport($store);
		$this->assertEquals($store->getStoreId(), $import1->getStoreId());
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());
		$this->assertNull($buffer->findInBufferDeficitTest($import1->getStoreId()));
		$buffer->addInBufferTest($import1);
		$buffer->addInBufferTest(self::getUniqueImport());
		$buffer->addInBufferTest(self::getUniqueImport());

		$this->assertEquals($import1->getImportId(), $buffer->findInBufferDeficitTest($import1->getStoreId())->getImportId());
		$import2 = self::getUniqueDeficitImport();
		$buffer->addInBufferTest($import2);
		$this->assertEquals($import2->getImportId(), $buffer->findInBufferDeficitTest($import2->getStoreId())->getImportId());
		$this->assertNotEquals($import1->getImportId(), $buffer->findInBufferDeficitTest($import2->getStoreId())->getImportId());
	}

	static function bufferProvider(){
		return [[new TestImportsBuffer()]];
	}
}

class TestImportsBuffer {
	use ImportsBuffer;

	function findInBufferTest(int $import_id): iImport|null {
		return $this->findInBuffer($import_id);
	}
	function findInBufferDeficitTest(int $store_id): iImport|null {
		return $this->findInBufferDeficit($store_id);
	}
	function addInBufferTest(iImport $import): void  {
		$this->addInBuffer($import);
	}
	function deleteFromBufferTest(int $id): void  {
		$this->deleteFromBuffer($id);
	}
}