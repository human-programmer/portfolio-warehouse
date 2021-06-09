<?php


namespace PragmaStorage\Test;


use PHPUnit\Framework\TestCase;
use PragmaStorage\iExport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\IProductImportModel;
use PragmaStorage\iStore;
use PragmaStorage\IStoreExportPriority;
use PragmaStorage\ProductImports;
use PragmaStorage\StorePriorityModel;
use const PragmaStorage\DEFICIT_SOURCE;
use const PragmaStorage\EXPORT_STATUS_EXPORTED;
use const PragmaStorage\EXPORT_STATUS_LINKED;
use const PragmaStorage\EXPORT_STATUS_RESERVED;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class ProductImportsMethodsTest extends \PHPUnit\Framework\TestCase {
	use ProductImportsCreator, ExportsCreator;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
		self::setDefaultPimportQuantity(10);
		self::setDefaultPurchasePrice(12);
	}

	public static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
//		self::clearProductImports();
//		self::clearProducts();
	}

	protected function setUp(): void {
		parent::setUp();
		TestPragmaFactory::resetStoreApp();
		self::setDefaultStore(self::getUniqueStore());
	}

	/**
	 * @dataProvider productImports
	 */
	function testPreloadedByProducts(ProductImports $imports){
		$product = self::getUniqueProduct();
		$product2 = self::getUniqueProduct();
		$productImports = self::uniqueProductImports(10, $product);
		$productImports2 = self::uniqueProductImports(11, $product2);

		$this->assertCount(0, $imports->getPreloadedProductsFromBuffer());
		$this->assertCount(0, $imports->getProductImportsFromBuffer());

		$imports->preloadProductImports([$product->getProductId()]);

		$this->assertCount(10, $imports->getProductImportsFromBuffer());
		$this->assertCount(1, $imports->getPreloadedProductsFromBuffer());

		$imports->preloadProductImports([$product2->getProductId()]);

		$this->assertCount(21, $imports->getProductImportsFromBuffer());
		$this->assertCount(2, $imports->getPreloadedProductsFromBuffer());
	}

	/**
	 * @dataProvider productImports
	 */
	function testGetProductDeficitCreate(ProductImports $imports){
		$store1 = self::getUniqueStore();
		$product1 = self::getUniqueProduct();
		$deficit = $imports->getProductDeficit($product1->getProductId(), $store1->getStoreId());
		$deficit2 = $imports->getProductDeficit($product1->getProductId(), $store1->getStoreId());
		$this->isDeficit($deficit, $product1, $store1);
		$this->assertTrue($deficit === $deficit2);
	}

	/**
	 * @dataProvider productImports
	 */
	function testGetProductDeficit(ProductImports $imports){
		$store1 = self::getUniqueStore();
		$product1 = self::getUniqueProduct();
		$store2 = self::getUniqueStore();
		$product2 = self::getUniqueProduct();
		$deficit1 = $imports->getProductDeficit($product1->getProductId(), $store1->getStoreId());
		$deficit2 = $imports->getProductDeficit($product2->getProductId(), $store2->getStoreId());
		$this->isDeficit($deficit1, $product1, $store1);
		$this->isDeficit($deficit2, $product2, $store2);
		$this->assertTrue($deficit1 !== $deficit2);
	}

	private function isDeficit(iProductImport $productImport, iProduct $product, iStore $store): void {
		$import = $productImport->findImport();
		$this->assertTrue($import->isDeficit());
		$this->assertEquals($store->getStoreId(), $import->getStoreId());

		$this->assertEquals(DEFICIT_SOURCE, $productImport->getSource());
		$this->assertEquals($product->getProductId(), $productImport->getProductId());
		$this->assertEquals($store->getStoreId(), $productImport->findStoreId());
		$this->assertTrue($productImport->isDeficit());
	}

	function testSetQuantityForSingleExport(): void {
		$imports = new ProductImports(TestPragmaFactory::getStoreApp());
		$product = self::getUniqueProduct();
		$productImportModel = self::uniqueEmptyProductImportModel($product);
		$productImport = $imports->create($productImportModel);
		$this->checkIsEmptyProductImport($productImportModel, $productImport);

		$export = self::createProductExportDeficit($product);
		$this->checkAllDetailIsDeficit($export);

		$checker = new ProductImportChecker($this);

		$this->checkDetailsQuantity($export, 1000);

		$checker->setProductImport1($productImport);
		$checker->setExport1($export);

		$checker->testSetQuantity(400, 0, 2);
		$checker->testSetQuantity(600, 0, 2);
		$checker->testSetQuantity(850, 0, 2);
		$checker->testSetQuantity(950, 0, 2);
		$checker->testSetQuantity(1001, 1, 1);
		$checker->testSetQuantity(1101, 101, 1);
		$checker->testSetQuantity(999, 0, 2);
	}

	/**
	 * @dataProvider productImports
	 */
	function testSetQuantityForManyExports(): void {
		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$product = self::getUniqueProduct();
		$export1 = self::getUniqueExportForProduct($product, 1000);
		$export2 = self::getUniqueExportForProduct($product, 1000);
		$priorities1 = [
			self::createPriority($export1, $store1, 1),
			self::createPriority($export1, $store2, 2),
		];
		$export1->setPriorities($priorities1);
		$priorities2 = [
			self::createPriority($export2, $store1, 2),
			self::createPriority($export2, $store2, 1),
		];
		$export2->setPriorities($priorities2);
		$export1->setStatus(EXPORT_STATUS_RESERVED);
		$export2->setStatus(EXPORT_STATUS_RESERVED);

		$productImport1 = self::createUniqueProductImport($product, $store1, 1400);
		$this->checkQuantities($export1, 1, 0, 1000);
		$this->checkQuantities($export2, 2, 600, 400);

		$productImport1->setQuantity(2000);
		$this->checkQuantities($export1, 1, 0, 1000);
		$this->checkQuantities($export2, 1, 0, 1000);

		$productImport1->setQuantity(2100);
		$this->checkQuantities($export1, 1, 0, 1000);
		$this->checkQuantities($export2, 1, 0, 1000);

		$productImport1->setQuantity(1700);
		$this->checkQuantities($export1, 1, 0, 1000);
		$this->checkQuantities($export2, 2, 300, 700);

		$productImport1->setQuantity(360);
		$this->checkQuantities($export1, 2, 640, 360);
		$this->checkQuantities($export2, 1, 1000, 0);
	}

	private function checkQuantities(iExport $export, int $expectDetailsQuantity, float $expectDeficit, float $expectQuantity): void {
		$export = TestPragmaFactory::getExports()->getExport($export->getExportId());
		$details = $export->getDetails();
		$this->assertCount($expectDetailsQuantity, $details);
		foreach ($details as $detail) {
			if($detail->isDeficit())
				$this->assertEquals($expectDeficit, $detail->getQuantity());
			else
				$this->assertEquals($expectQuantity, $detail->getQuantity());
		}
	}

	function testBalanceQuantityFromExport(): void {
		$store1 = self::getUniqueStore();
		$product = self::getUniqueProduct();
		$export1 = self::getUniqueExportForProduct($product, 1000);
		$export2 = self::getUniqueExportForProduct($product, 1000);
		$productImport1 = self::createUniqueProductImport($product, $store1, 1400);

		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,1400, 1400);

		$export1->setStatus(EXPORT_STATUS_RESERVED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,400, 1400);

		$export1->setStatus(EXPORT_STATUS_EXPORTED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,400, 400);

		$export2->setStatus(EXPORT_STATUS_EXPORTED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,0, 0);

		$export2->setStatus(EXPORT_STATUS_RESERVED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,0, 400);

		$export2->setStatus(EXPORT_STATUS_LINKED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,400, 400);

		$export1->setStatus(EXPORT_STATUS_LINKED);
		$this->checkCurrentAndActualBalanceQuantities($productImport1, 1400,1400, 1400);
	}

	private function checkCurrentAndActualBalanceQuantities(iProductImport $productImport, float $expect_import, float $expect_free, float $expect_balance): void {
		$imports = new ProductImports(TestPragmaFactory::getStoreApp());
		$actual = $imports->getProductImport($productImport->getProductImportId());

		$this->assertEquals($expect_import, $actual->getImportQuantity());
		$this->assertEquals($expect_free, $actual->getFreeBalanceQuantity());
		$this->assertEquals($expect_balance, $actual->getBalanceQuantity());
	}

	private static function createUniqueProductImport(iProduct $product, iStore $store, float $quantity): iProductImport {
		self::setDefaultPimportQuantity($quantity);
		return self::getUniqueProductImportForStore($product, $store);
	}

	private static function createPriority(iExport $export, iStore $store, int $sort): IStoreExportPriority {
		return new StorePriorityModel([
			'export_id' => $export->getExportId(),
			'store_id' => $store->getStoreId(),
			'sort' => $sort,
		]);
	}

	private function createProductExportDeficit(iProduct $product): iExport {
		$export = self::getUniqueExportForProduct($product, 1000);
		$export->setStatus(EXPORT_STATUS_RESERVED);
		return $export;
	}

	private function checkIsEmptyProductImport(IProductImportModel $expect, iProductImport $actual): void {
		$this->assertTrue(!!$actual->getProductImportId());
		$this->assertEquals($expect->getProductId(), $actual->getProductId());
		$this->assertEquals($expect->getImportId(), $actual->getImportId());
		$this->assertEquals($expect->getImportQuantity(), $actual->getImportQuantity());
		$this->assertEquals($expect->getSource(), $actual->getSource());
		$this->assertEquals($expect->isDeficit(), $actual->isDeficit());
		$this->assertEquals($expect->getFreeBalanceQuantity(), $actual->getFreeBalanceQuantity());

		$this->assertEquals(0, $actual->getFreeBalanceQuantity());
		$this->assertEquals(0, $actual->getImportQuantity());
		$this->assertFalse($actual->isDeficit());
	}

	private function checkAllDetailIsDeficit(iExport $export): void {
		$details = $export->getDetails();
		$this->assertTrue(count($details) > 0);
		foreach ($details as $detail)
			$this->assertTrue($detail->isDeficit());
	}

	private function checkDetailsQuantity(iExport $export, float $expected): void {
		$this->assertEquals($expected, $export->getDetailsQuantity());
	}

	static function productImports(){
		TestPragmaFactory::ifInitTest();
		return [[new ProductImports(TestPragmaFactory::getStoreApp())]];
	}
}

class ProductImportChecker {
	private iExport $export1;
	private iProductImport $productImport1;

	function __construct(private TestCase $testObj) {}

	function getExport1(): iExport {
		return TestPragmaFactory::getExports()->getExport($this->export1->getExportId());
	}

	function setExport1(iExport $export1): void {
		$this->export1 = $export1;
	}

	function getProductImport1(): iProductImport {
		return $this->productImport1;
	}

	function setProductImport1(iProductImport $productImport1): void {
		$this->productImport1 = $productImport1;
	}

	function testSetQuantity(float $newQuantity, float $freeExpected, int $expectedExports): void {
		$productImport = $this->getProductImport1();
		$productImport->setQuantity($newQuantity);
		$this->testObj->assertEquals($freeExpected, $productImport->getFreeBalanceQuantity());
		$this->testObj->assertCount($expectedExports, $this->getExport1()->getDetails());
	}
}