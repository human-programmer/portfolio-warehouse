<?php


namespace PragmaStorage\Exports\Tests;


use PragmaStorage\PrioritiesIterator;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class StorePrioritiesIteratorTest extends \PHPUnit\Framework\TestCase {
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
		TestPragmaFactory::resetStoreApp();self::clearPriorities();
	}

	function testLoop(): void {
		$dataSet = self::createDataSet();
		$iterator = new PrioritiesIterator($dataSet['rand_sort']);

		foreach ($iterator as $index => $model)
			$this->assertTrue($model === $dataSet['expect-sort'][$index]);
	}

	static function createDataSet(): array {
		$stores = self::getUniqueStores(5);
		$export1 = self::getUniqueExport();
		$models = self::uniquePriorityModels($stores, $export1);
		return [
			'rand_sort' => [
				$models[1],
				$models[3],
				$models[4],
				$models[2],
				$models[0]
			],
			'expect-sort' => $models
		];
	}
}