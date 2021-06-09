<?php


namespace PragmaStorage\Test;

use PragmaStorage\iImport;
use PragmaStorage\Imports;
use PragmaStorage\iStore;
use const PragmaStorage\DEFICIT_SOURCE;

require_once __DIR__ . '/../../TestPragmaFactory.php';


class ImportsMethodsTest extends \PHPUnit\Framework\TestCase {
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
	 * @dataProvider importsProvider
	 */
	function testGetDeficitImportCreate(Imports $imports): void {
		$store1 = self::getUniqueStore();
		$deficitImport = $imports->getDeficitImport($store1->getStoreId());
		$this->isDeficitImport($deficitImport, $store1);
		$this->assertTrue($deficitImport === $imports->getDeficitImport($store1->getStoreId()));
	}

	/**
	 * @dataProvider importsProvider
	 */
	function testGetDeficitImport(Imports $imports): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$deficitImport1 = $imports->getDeficitImport($store1->getStoreId());
		$deficitImport2 = $imports->getDeficitImport($store2->getStoreId());
		$this->isDeficitImport($deficitImport1, $store1);
		$this->isDeficitImport($deficitImport2, $store2);
		$this->assertTrue($deficitImport1 !== $deficitImport2);
	}

	private function isDeficitImport(iImport $import, iStore $store): void {
		$this->assertEquals(DEFICIT_SOURCE, $import->getSource());
		$this->assertTrue($import->isDeficit());
		$this->assertEquals($store->getStoreId(), $import->getStoreId());
		$this->assertTrue(!!$import->getNumber());
		$this->assertTrue(!!$import->getTimeCreate());
	}

	static function importsProvider(){
		TestPragmaFactory::ifInitTest();
		return [[new Imports(TestPragmaFactory::getPragmaAccountId())]];
	}
}