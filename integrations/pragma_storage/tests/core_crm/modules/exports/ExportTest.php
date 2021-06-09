<?php


namespace PragmaStorage\Test;


use PHPUnit\Framework\TestCase;
use PragmaStorage\iExport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\iStore;
use PragmaStorage\IStoreExportPriority;
use PragmaStorage\StorePriorityModel;
use const PragmaStorage\EXPORT_STATUS_EXPORTED;
use const PragmaStorage\EXPORT_STATUS_LINKED;
use const PragmaStorage\EXPORT_STATUS_RESERVED;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class ExportTest extends \PHPUnit\Framework\TestCase {
	use ExportsCreator;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
		self::setDefaultEntity(self::getUniqueEntity());
	}

	protected function setUp(): void {
		parent::setUp();
		TestPragmaFactory::resetStoreApp();
	}

	function testDistributeByPriority(): void {
		$checker = new DistributePriorityChecker($this);

		$checker->testDistribute();
		$checker->testDistributeDeficit();
		$checker->testDistributeDeficitWithChangedPriorities();
	}

    function testTotalPurchasePrice(): void {
	    $product = self::getUniqueProduct();
	    $productImport1 = self::createUniqueProductImport($product, 1, 10);
	    $productImport2 = self::createUniqueProductImport($product, 10, 100);
	    $productImport3 = self::createUniqueProductImport($product, 100, 1000);
	    $total_quantity = 111;
	    $total_purchase_price = 1 * 10 + 10 * 100 + 100 * 1000;
	    $spec_cost = $total_purchase_price / $total_quantity;

        $export = self::getUniqueExportForProduct($product, $total_quantity);
        $export->setStatus(EXPORT_STATUS_EXPORTED);
        $this->assertEquals($total_purchase_price, $export->getTotalPurchasePrice());

        $with_deficit = 1000 * $spec_cost + $total_purchase_price;
        $export->setQuantity($export->getQuantity() + 1000);
        $this->assertEquals($with_deficit, $export->getTotalPurchasePrice());
    }

    private static function createUniqueProductImport(iProduct $product, float $quantity, float $purchasePrice): iProductImport {
	    self::setDefaultPimportQuantity($quantity);
	    self::setDefaultPurchasePrice($purchasePrice);
	    return self::getUniqueProductImport(null, $product);
    }
}

class DistributePriorityChecker{
	use ExportsCreator;

	private	iProduct $product;
	private	iStore $store1;
	private	iStore $store2;
	private	iStore $store3;
	private	iProductImport $productImport1;
	private	iProductImport $productImport2;
	private	iProductImport $productImport3;
	private	iExport $export;
	private	IStoreExportPriority $priority1;
	private	IStoreExportPriority $priority2;
	private	IStoreExportPriority $priority3;
	private float $importsSum = 600;

	function __construct(private TestCase $test) {
		$this->store1 = self::getUniqueStore();
		$this->store2 = self::getUniqueStore();
		$this->store3 = self::getUniqueStore();
        $this->product = self::getUniqueProduct();
        $this->productImport1 = self::createTestProductImport($this->product, $this->store1, 100);
		$this->productImport2 = self::createTestProductImport($this->product, $this->store2, 200);
		$this->productImport3 = self::createTestProductImport($this->product, $this->store3, 300);

		TestPragmaFactory::resetStoreApp();

		$this->export = $this->createUniqueExport();
		$this->test->assertEquals(EXPORT_STATUS_LINKED, $this->export->getStatusId());

		$this->priority1 = self::createPriority($this->export, $this->store1, 2);
		$this->priority2 = self::createPriority($this->export, $this->store2, 0);
		$this->priority3 = self::createPriority($this->export, $this->store3, 1);
		$priorities = [$this->priority1, $this->priority2, $this->priority3];
		$this->export->setPriorities($priorities);
	}

	private function createUniqueExport(): iExport {
	    return self::getUniqueExportForProduct($this->product, 70);
    }

	private static function createTestProductImport(iProduct $product, iStore $store, float $quantity): iProductImport {
		self::setDefaultPimportQuantity($quantity);
		$productImport = self::getUniqueProductImportForStore($product, $store);
		$productImport->setQuantity($quantity);
		return $productImport;
	}

	private static function createPriority(iExport $export, iStore $store, int $sort): IStoreExportPriority {
		return new StorePriorityModel([
			'export_id' => $export->getExportId(),
			'store_id' => $store->getStoreId(),
			'sort' => $sort
		]);
	}

	function testDistribute(): void {
		$this->export->setStatus(EXPORT_STATUS_RESERVED);
		$export = $this->export;
		$productImport2 = $this->productImport2;
		$productImport3 = $this->productImport3;
		$productImport1 = $this->productImport1;

		$this->checkProductImports($export, [$productImport2], [$productImport2->getProductImportId() => 130]);

		$export->setQuantity(210);
		$this->checkProductImports($export, [$productImport2, $productImport3], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 290]);

		$export->setQuantity(310);
		$this->checkProductImports($export, [$productImport2, $productImport3], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 190]);

		$export->setQuantity(490);
		$this->checkProductImports($export, [$productImport2, $productImport3], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 10]);

		$export->setQuantity(140);
		$this->checkProductImports($export, [$productImport2], [$productImport2->getProductImportId() => 60]);

		$export->setQuantity(590);
		$this->checkProductImports($export, [$productImport2, $productImport3, $productImport1], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 0, $productImport1->getProductImportId() => 10]);

		$export->setQuantity(600);
		$this->checkProductImports($export, [$productImport2, $productImport3, $productImport1], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 0, $productImport1->getProductImportId() => 0]);


		$export->setStatus(EXPORT_STATUS_LINKED);

		$this->assertEquals(0, $export->getDetailsQuantity());
		$export->setStatus(EXPORT_STATUS_EXPORTED);

		$this->assertEquals(600, $export->getDetailsQuantity());
		$this->checkProductImports($export, [$productImport2, $productImport3, $productImport1], [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 0, $productImport1->getProductImportId() => 0]);

	}

	function testDistributeDeficit(): void {
		$export = $this->export;
		$product = $this->product;
		$productImport2 = $this->productImport2;
		$productImport3 = $this->productImport3;
		$productImport1 = $this->productImport1;
		$priorities = $this->getPriorities();

		$this->export->setPriorities($priorities);

		$export->setQuantity(700);

		$this->assertEquals(700, $export->getDetailsQuantity());

		$expected_deficit = self::getExpectedDeficit($priorities, $product->getProductId());

		$other_priorities = [$productImport2, $productImport3, $productImport1];
		$other_free = [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 0, $productImport1->getProductImportId() => 0, $expected_deficit->getProductImportId() => -100];

		$this->checkProductImports($export, array_merge($other_priorities, [$expected_deficit]), $other_free);
	}

	function testDistributeDeficitWithChangedPriorities(): void {
		$product = $this->product;

		$priority1 = self::createPriority($this->export, $this->store1, 1);
		$priority2 = self::createPriority($this->export, $this->store2, 2);
		$priority3 = self::createPriority($this->export, $this->store3, 3);

		$oldPriorities = $this->getPriorities();
		$newPriorities = [$priority1, $priority2, $priority3];

		$expected_deficit = self::getExpectedDeficit($oldPriorities, $product->getProductId());
		$this->testPrioritiesIteration($newPriorities, 800, $expected_deficit);

		$expected_deficit = self::getExpectedDeficit($oldPriorities, $product->getProductId());
		$this->testPrioritiesIteration($newPriorities, 900, $expected_deficit);

		$this->testPrioritiesIteration($newPriorities, 600, null);

		$expected_deficit1 = self::getExpectedDeficit($newPriorities, $product->getProductId());
		$this->assertTrue($expected_deficit1->getProductImportId() !== $expected_deficit->getProductImportId());
		$this->testPrioritiesIteration($newPriorities, 900, $expected_deficit1);
	}

//	function testAvailableStoresFilter(): void {
//	    $this->product = self::getUniqueProduct();
//	    $this->export = $this->createUniqueExport();
//	    $this->export->setStatus(EXPORT_STATUS_LINKED);
//	    $this->export->setAvailableStoresId([$this->store2->getStoreId()]);
//
//        $priority1 = self::createPriority($this->export, $this->store1, 1);
//        $priority2 = self::createPriority($this->export, $this->store2, 2);
//        $priority3 = self::createPriority($this->export, $this->store3, 3);
//
//        $productImport1 = self::getUniqueProductImportForStore($this->product, $this->store1, 100);
//        $productImport2 = self::getUniqueProductImportForStore($this->product, $this->store2, 200);
//        $productImport3 = self::getUniqueProductImportForStore($this->product, $this->store3, 300);
//
//        $this->checkFreeProductQuantity($this->store1, 100);
//        $this->checkFreeProductQuantity($this->store2, 200);
//        $this->checkFreeProductQuantity($this->store3, 300);
//
//        $this->export->setQuantity(300);
//        $this->export->setStatus(EXPORT_STATUS_EXPORTED);
//
//        $this->checkFreeProductQuantity($this->store1, 100);
//        $this->checkFreeProductQuantity($this->store2, -100);
//        $this->checkFreeProductQuantity($this->store3, 300);
//    }

    private function checkFreeProductQuantity(iStore $target_store, float $expectQuantity): void {
	    TestPragmaFactory::resetStoreApp();
	    $target_store = TestPragmaFactory::getTestStores()->getStore($target_store->getStoreId());
	    $products_imports = $target_store->getOwnProductImports();
	    $actual = 0.0;
	    foreach($products_imports as $product_import)
	        if($this->product->getProductId() === $product_import->getProductId())
                $actual += $product_import->getFreeBalanceQuantity();
        $this->assertEquals($expectQuantity, $actual);
    }

	private function testPrioritiesIteration(array $newPriorities, float $newExportQuantity, iProductImport|null $expected_deficit): void {
		$export = $this->export;
		$productImport2 = $this->productImport2;
		$productImport3 = $this->productImport3;
		$productImport1 = $this->productImport1;
		$expectedDif = $this->importsSum - $newExportQuantity;

		$export->setPriorities($newPriorities);
		$export->setQuantity($newExportQuantity);
		$this->assertEquals($newExportQuantity, $export->getDetailsQuantity());

		$other_priorities = [$productImport2, $productImport3, $productImport1];

		$other_free = [$productImport2->getProductImportId() => 0, $productImport3->getProductImportId() => 0, $productImport1->getProductImportId() => 0];
		if($expected_deficit)
			$other_free[$expected_deficit->getProductImportId()] = $expectedDif;

		$this->checkProductImports($export, array_merge($other_priorities, $expected_deficit ? [$expected_deficit] : []), $other_free);
	}

	private function checkProductImports(iExport $export, array $expectedImports, array $expectedImportsFree): void {
		foreach ($expectedImports as $item)
			$expectedProductsImports[$item->getProductImportid()] = $item;

		$this->assertTrue(count($expectedImports) === count($expectedImportsFree));
		$actual = $export->getProductsImports();
		$actual = self::filterEmptyImports($actual);

		foreach ($actual as $actualProductImport)
			$this->assertTrue(isset($expectedProductsImports[$actualProductImport->getProductImportId()]));

		$this->assertCount(count($expectedImports), $actual);

		foreach ($actual as $item)
			$actualProductImports[$item->getProductImportid()] = $item;

		foreach ($actualProductImports as $actualProductImport) {
			$this->assertTrue(isset($expectedImportsFree[$actualProductImport->getProductImportId()]));
			$this->assertEquals($expectedImportsFree[$actualProductImport->getProductImportId()], $actualProductImport->getFreeBalanceQuantity());
		}
	}

	private static function filterEmptyImports(array $productsImports): array {
	    foreach($productsImports as $i)
	        if($i->getImportQuantity() || $i->getFreeBalanceQuantity() || $i->getBalanceQuantity())
	            $result[] = $i;
        return $result ?? [];
    }

	private static function getExpectedDeficit(array $priorities, int $product_id): iProductImport {
		foreach ($priorities as $priority)
			$arr[$priority->getSort()] = $priority;
		ksort($arr);
		$maxPriority = array_values($arr)[0];
		return TestPragmaFactory::getProductImports()->getProductDeficit($product_id, $maxPriority->getStoreId());
	}

	function assertEquals($expected, $actual): void {
		$this->test->assertEquals($expected, $actual);
	}

	function assertTrue($res): void {
		$this->test->assertTrue($res);
	}

	function assertCount($expected, $arr): void {
		$this->test->assertCount($expected, $arr);
	}

	private function getPriorities(): array {
		return [$this->priority1, $this->priority2, $this->priority3];
	}
}