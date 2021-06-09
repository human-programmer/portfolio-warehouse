<?php


namespace PragmaStorage\Test;



use PragmaStorage\iProduct;
use PragmaStorage\ProductsCatalog;
use const PragmaStorage\EXPORT_STATUS_EXPORTED;
use const PragmaStorage\EXPORT_STATUS_LINKED;
use const PragmaStorage\EXPORT_STATUS_RESERVED;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/../../../../core_crm/modules/catalog/ProductsCatalog.php';


class ProductsCatalogTest extends \PHPUnit\Framework\TestCase {
	use StoresCreator, ExportsCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
	}

	function setUp(): void {
		parent::setUp();
		TestPragmaFactory::resetStoreApp();
	}

	function testWithoutProductImports(): void {
	    $store1 = self::getUniqueStore();
	    $store2 = self::getUniqueStore();
        $stores = [$store1, $store2];

	    $product1 = self::getUniqueProduct();
	    $product2 = self::getUniqueProduct();
	    $product3 = self::getUniqueProduct();
	    $product4 = self::getUniqueProduct();
	    $products = [$product1, $product2, $product3, $product4];

	    $this->checkFullFilter($stores, $products);
    }

    private function checkFullFilter(array $stores, array $products): void {
	    $catalog = self::createProductsCatalog($stores, $products);
	    $actual_products = $catalog->getProducts();

	    $this->assertCount(count($products), $actual_products);

	    foreach($products as $product)
	        $this->checkQuantities($product);
    }

    private function checkQuantities(iProduct $product): void {
	    self::createProductsImportsForProduct($product);
	    $this->validQuantity($product, 1000, 1000);

	    $exports = self::createExports($product, 300);
        $this->validQuantity($product, 1000, 1000);

        self::setExportsStatus($exports, EXPORT_STATUS_RESERVED);
        $this->validQuantity($product, 700, 1000);

        self::setExportsStatus($exports, EXPORT_STATUS_EXPORTED);
        $this->validQuantity($product, 700, 700);

        self::setExportsStatus($exports, EXPORT_STATUS_RESERVED);
        $this->validQuantity($product, 700, 1000);

        self::setExportsStatus($exports, EXPORT_STATUS_LINKED);
        $this->validQuantity($product, 1000, 1000);

        self::setExportsStatus($exports, EXPORT_STATUS_EXPORTED);
        $this->validQuantity($product, 700, 700);

        $exports[0]->setStatus(EXPORT_STATUS_LINKED);
        $this->validQuantity($product, 730, 730);
    }

    private function validQuantity(iProduct $product, float $expect_free_quantity, float $expect_balance): void {
	    $catalog = self::createProductsCatalog([], [$product]);
	    $product_row = $catalog->getProducts();
	    $this->assertCount(1, $product_row);
	    $this->assertEquals($expect_free_quantity, $product_row[0]['free_balance']);
	    $this->assertEquals($expect_balance, $product_row[0]['balance']);
    }

    private static function createProductsImportsForProduct(iProduct $product): void {
        self::setDefaultPimportQuantity(100);
        self::uniqueProductImports(10, $product);
    }

    private static function createProductsCatalog(array $stores, array $products): ProductsCatalog {
	    $filter = self::createFilter($stores, $products);
	    return self::createCatalogFromFilter($filter);
    }

    private static function createCatalogFromFilter(array $filter): ProductsCatalog {
        return new ProductsCatalog(TestPragmaFactory::getPragmaAccountId(), $filter);
    }

    private static function createFilter(array $stores, array $products): array {
        $products_id = self::productsId($products);
        $categories_id = self::categoryId($products);
        $stores_id = self::storesId($stores);
	    return [
            'id' => $products_id,
            'category_id' => $categories_id,
            'store_id' => $stores_id,
        ];
    }

    private static function categoryId(array $products): array {
	    foreach($products as $product)
	        $id[] = $product->getCategoryId();
	    return $id ?? [];
    }

    private static function productsId(array $products): array {
        foreach($products as $product)
            $id[] = $product->getProductId();
        return $id ?? [];
    }

    private static function storesId(array $stores): array {
	    foreach($stores as $store)
	        $id[] = $store->getStoreId();
        return $id ?? [];
    }

    private static function createExports(iProduct $product, float $export_quantity): array {
        for($i = 0; $i < 10; $i++)
            $exports[] = self::getUniqueExportForProduct($product, $export_quantity / 10);
        return $exports;
    }

    private static function setExportsStatus(array $exports, int $status): void {
	    foreach($exports as $export)
	        $export->setStatus($status);
    }



    function testSearch(): void {
        $store1 = self::getUniqueStore();
        $store2 = self::getUniqueStore();
        $stores = [$store1, $store2];

        $product1 = self::getUniqueProduct();
        $product2 = self::getUniqueProduct();
        $product3 = self::getUniqueProduct();
        $product4 = self::getUniqueProduct();
        $products = [$product1, $product2, $product3, $product4];

        $filterModel = self::createFilter($stores, $products);
        $filterModel['search'] = self::fetchLastSubstr($product1->getArticle(), 5);
        $this->checkSearchByField($filterModel, $product1);

        $filterModel = self::createFilter($stores, $products);
        $filterModel['search'] = self::fetchLastSubstr($product4->getTitle(), 5);
        $this->checkSearchByField($filterModel, $product4);
    }

    private function checkSearchByField(array $filter, iProduct $expect_product): void {
        $catalog = self::createCatalogFromFilter($filter);
        $products = $catalog->getProducts();
        $this->assertEquals($expect_product->getProductId(), $products[0]['id']);
        $this->assertEquals($expect_product->getArticle(), $products[0]['article']);
        $this->assertEquals($expect_product->getTitle(), $products[0]['title']);
    }

    function testEmptyFilter(): void {
        $product1 = self::getUniqueProduct();
        $product2 = self::getUniqueProduct();
        $product3 = self::getUniqueProduct();
        $product4 = self::getUniqueProduct();

        $filterModel = self::createFilter([], []);
        $catalog = self::createCatalogFromFilter([]);
        $products = $catalog->getProducts();
        $this->assertTrue(count($products) >= 4);
    }

    private static function fetchLastSubstr(string $str, int $lastLength): string {
	    return substr($str, -1 * $lastLength);
    }
}