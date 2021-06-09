<?php


namespace PragmaStorage\Test;


use PHPUnit\Framework\TestCase;
use PragmaStorage\Exports;
use PragmaStorage\Factory;
use PragmaStorage\iExport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\iStore;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/../products/ProductsCreator.php';
require_once __DIR__ . '/../stores/StoresCreator.php';
require_once __DIR__ . '/../entities/EntitiesCreator.php';
require_once __DIR__ . '/../product_imports/ProductImportsCreator.php';
require_once __DIR__ . '/ExportsCreator.php';

class ExportsTest extends TestCase {
	use ProductsCreator, StoresCreator, EntitiesCreator, ProductImportsCreator, ExportsCreator;

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
		TestPragmaFactory::resetStores();
		self::setDefaultPimportQuantity(10);
		self::setDefaultSellingPrice(4.5);
		self::setDefaultEntity(self::getUniqueEntity());
	}

	protected function setUp(): void {
		parent::setUp();
		TestPragmaFactory::resetStoreApp();
	}

	function testCreate (){
		$product = $this->getUniqueProduct();
		$store = $this->getUniqueStore();
		$model = [
			'product' => $product,
			'quantity' => 5.56,
			'selling_price' => 5.5,
		];
		$export = TestPragmaFactory::getExports()->createExports($this->getUniqueEntity(), [$model])[0];

		$exports = new Exports(TestPragmaFactory::getStoreApp());
		$actualExport = $exports->getExport($export->getExportId());
		$this->assertInstanceOf(iExport::class, $actualExport);
	}

	function testPriorityStoreDeficit(){
		$stores = self::getUniqueStores(10);
		$targetStore = $stores[5];
		$category = TestPragmaFactory::getCategories()->createCategory('sdf', [$targetStore->getStoreId()]);

		$product = self::getUniqueProduct($category);

		$export = self::getUniqueExportForProduct($product);
		$priority = $export->getHighestPriority();

		$this->assertEquals($targetStore->getStoreId(), $priority->getStoreId());

		$product = TestPragmaFactory::getProducts()->getProduct($product->getProductId());

		TestPragmaFactory::resetStoreApp();

		$stores[6]->addCategory($category);
		$priority = $export->getHighestPriority();
		$this->assertEquals($targetStore->getStoreId(), $priority->getStoreId());

		TestPragmaFactory::resetStoreApp();
		$stores[4]->addCategory($category);
		$export = TestPragmaFactory::getExports()->getExport($export->getExportId());
		$priority = $export->getHighestPriority();
		$this->assertEquals($stores[4]->getStoreId(), $priority->getStoreId());
	}

	private function checkDetails(array $details, int $pid): void {
		foreach ($details as $detail)
			if($detail->getProductImport()->getProductImportId() === $pid || $detail->getProductImport()->isDeficit())
				$this->assertEquals(0, $detail->getProductImport()->getFreeBalanceQuantity());
			else
				$this->assertEquals(5, $detail->getProductImport()->getFreeBalanceQuantity());
	}

	private static function createTestProductImportForStores(iProduct $product, iStore|null $store = null): iProductImport {
		$product_import0 = self::getUniqueProductImportForStore($product, $store);
		self::setQuantity($product_import0);
		return $product_import0;
	}

	private static function setQuantity(iProductImport $import, int $quantity = 10): void {
		$import->update(['quantity' => $quantity]);
	}
}