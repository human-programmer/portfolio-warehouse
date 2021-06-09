<?php


namespace PragmaStorage\Exports\Tests;


use PragmaStorage\StorePriorities;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class StorePrioritiesTest extends \PHPUnit\Framework\TestCase {
	use PrioritiesCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		self::clearPriorities();
	}

	protected function setUp(): void {
		parent::setUp();
		self::clearPriorities();
		self::clearStores();
		TestPragmaFactory::resetStoreApp();
	}

	function testCreate(): void {
		$fabric = new StorePriorities(TestPragmaFactory::getStoreApp());
		$this->assertInstanceOf(StorePriorities::class, $fabric);
	}

	function testGetPriorities(): void {
		$stores = self::getUniqueStores(10);
		$export1 = self::getUniqueExport();
		$export2 = self::getUniqueExport();
		$priorities1 = self::uniquePriorityModels($stores, $export1);
		$priorities2 = self::uniquePriorityModels($stores, $export2);
		$fabric = new StorePriorities(TestPragmaFactory::getStoreApp());
		$props = $fabric->getPriorities([$export1->getExportId(), $export2->getExportId()]);
		$this->comparePriorities(10, $priorities1, $props[$export1->getExportId()]);
		$this->comparePriorities(10, $priorities2, $props[$export2->getExportId()]);
	}

	function testClear(): void {
		$stores = self::getUniqueStores(15);
		$stores = TestPragmaFactory::getStores()->getStores();
		$this->assertTrue(count($stores) >= 15);
		$stores1 = array_merge([], $stores);
		array_splice($stores1, 0, count($stores1) - 5);
		$export1 = self::getUniqueExport();
		$export2 = self::getUniqueExport();
		$priorities1 = self::uniquePriorityModels($stores1, $export1);
		$priorities2 = self::uniquePriorityModels($stores1, $export2);
		$fabric = new StorePriorities(TestPragmaFactory::getStoreApp());

		$props = $fabric->getPriorities([$export1->getExportId(), $export2->getExportId()]);

		$this->comparePriorities(5, $priorities1, $props[$export1->getExportId()]);
		$this->comparePriorities(5, $priorities2, $props[$export2->getExportId()]);

		$fabric->savePriorities($export1->getExportId(), []);
		$props = $fabric->getPriorities([$export1->getExportId(), $export2->getExportId()]);
		$this->assertCount(count($stores), $props[$export1->getExportId()]);
		$this->assertCount(5, $props[$export2->getExportId()]);
	}

	function comparePriorities(int $expectQuantity, array $expected, array $actual): void {
		$this->assertCount($expectQuantity, $expected);
		$this->assertCount($expectQuantity, $actual);

		foreach ($expected as $exp)
			$exp2[$exp->getStoreId()] = $exp;

		foreach ($actual as $act) {
			$expect = $exp2[$act->getStoreId()];
			$this->assertEquals($expect->getStoreId(), $act->getStoreId());
			$this->assertEquals($expect->getExportId(), $act->getExportId());
			$this->assertEquals($expect->getSort(), $act->getSort());
		}
	}
}