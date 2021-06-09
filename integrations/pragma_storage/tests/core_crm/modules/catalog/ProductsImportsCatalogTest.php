<?php


namespace PragmaStorage\Test;


use PragmaStorage\ProductsImportsCatalog;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/../../../../core_crm/modules/catalog/ProductsImportsCatalog.php';

class ProductsImportsCatalogTest extends \PHPUnit\Framework\TestCase {
    use StoresCreator, ProductImportsCreator;

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

    function testEmptyFilter(): void {
        $store = self::getUniqueStore();
        $product1 = self::getUniqueProductForStore([$store->getStoreId()]);
        $product2 = self::getUniqueProductForStore([$store->getStoreId()]);
        $product_import1_1 = self::getUniqueProductImportForStore($product1, $store);
        $product_import1_2 = self::getUniqueProductImportForStore($product1, $store);
        $product_import2_1 = self::getUniqueProductImportForStore($product2, $store);
        $product_import2_2 = self::getUniqueProductImportForStore($product2, $store);

        $catalog = self::createCatalogFromFilter([]);
        $products = $catalog->getProductsImports();
        $this->assertTrue(count($products) >= 4);
    }

    function testProductsFilter(): void {
        $store = self::getUniqueStore();
        $product1 = self::getUniqueProductForStore([$store->getStoreId()]);
        $product2 = self::getUniqueProductForStore([$store->getStoreId()]);
        $product_import1_1 = self::getUniqueProductImportForStore($product1, $store);
        $product_import1_2 = self::getUniqueProductImportForStore($product1, $store);
        $product_import2_1 = self::getUniqueProductImportForStore($product2, $store);
        $product_import2_2 = self::getUniqueProductImportForStore($product2, $store);

        $catalog = self::createCatalogFromFilter(['product_id' => $product1->getProductId()]);
        $products = $catalog->getProductsImports();
        $this->assertCount(2, $products);

        $catalog = self::createCatalogFromFilter(['product_id' => [$product1->getProductId(), $product2->getProductId()]]);
        $products = $catalog->getProductsImports();
        $this->assertCount(4, $products);

        $catalog = self::createCatalogFromFilter(['product_id' => [$product1->getProductId(), $product2->getProductId()], 'id' => $product_import1_1->getProductImportId()]);
        $products = $catalog->getProductsImports();
        $this->assertCount(1, $products);
    }

    function testStoresFilter(): void {
        $store1 = self::getUniqueStore();
        $store2 = self::getUniqueStore();
        $product1 = self::getUniqueProductForStore([$store1->getStoreId()]);
        $product2 = self::getUniqueProductForStore([$store2->getStoreId()]);
        $product_import1_1 = self::getUniqueProductImportForStore($product1, $store1);
        $product_import1_2 = self::getUniqueProductImportForStore($product1, $store1);
        $product_import2_1 = self::getUniqueProductImportForStore($product2, $store2);
        $product_import2_2 = self::getUniqueProductImportForStore($product2, $store2);

        $catalog = self::createCatalogFromFilter(['store_id' => $store1->getStoreId()]);
        $products = $catalog->getProductsImports();
        $this->assertCount(2, $products);

        $catalog = self::createCatalogFromFilter(['store_id' => $store1->getStoreId(), 'import_id' => $product_import1_1->getImportId()]);
        $products = $catalog->getProductsImports();
        $this->assertCount(1, $products);
    }


    private static function createCatalogFromFilter(array $filter = []): ProductsImportsCatalog {
        return new ProductsImportsCatalog(TestPragmaFactory::getPragmaAccountId(), $filter);
    }
}