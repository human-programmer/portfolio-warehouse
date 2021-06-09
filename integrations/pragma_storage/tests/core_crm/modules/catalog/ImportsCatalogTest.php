<?php


namespace PragmaStorage\Test;


use Generals\Functions\Date;
use PHPUnit\Framework\TestCase;
use PragmaStorage\iImport;
use PragmaStorage\ImportsCatalog;
use PragmaStorage\iStore;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/../../../../core_crm/modules/catalog/ImportsCatalog.php';

class ImportsCatalogTest extends TestCase {
    use ImportsCreator;

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
        $import1 = self::getUniqueImport();
        $import2 = self::getUniqueImport();
        $import3 = self::getUniqueImport();
        $import4 = self::getUniqueImport();
        $catalog = self::createCatalogFromFilter();
        $this->assertTrue(count($catalog->getImports()) >= 4);
    }

    function testFilterOfStores(): void {
        $store1 = self::getUniqueStore();
        $store2 = self::getUniqueStore();
        $import1_1 = self::getUniqueImport($store1);
        $import1_2 = self::getUniqueImport($store1);
        $import2_1 = self::getUniqueImport($store2);
        $import2_2 = self::getUniqueImport($store2);

        $catalog = self::createCatalogFromFilter(['store_id' => $store1->getStoreId()]);
        $this->assertCount(2, $catalog->getImports());

        $catalog = self::createCatalogFromFilter(['store_id' => [$store1->getStoreId(), $store2->getStoreId()]]);
        $this->assertCount(4, $catalog->getImports());

        $catalog = self::createCatalogFromFilter(['store_id' => [$store1->getStoreId(), $store2->getStoreId()], 'id' => $import1_1->getImportId()]);
        $this->assertCount(1, $catalog->getImports());
    }

    function testDateConditions(): void {
        $store = self::getUniqueStore();
        $import1 = self::createImportWithDate($store, 2);
        $import2 = self::createImportWithDate($store, 3);
        $import3 = self::createImportWithDate($store, 1);

        $catalog = self::createCatalogFromFilter(['store_id' => $store->getStoreId()]);
        $imports = $catalog->getImports();
        $this->checkOrder([$import2, $import1, $import3], $imports);

        $catalog = self::createCatalogFromFilter(['store_id' => $store->getStoreId(), 'order' => 'desc']);
        $imports = $catalog->getImports();
        $this->checkOrder([$import2, $import1, $import3], $imports);

        $catalog = self::createCatalogFromFilter(['store_id' => $store->getStoreId(), 'order' => 'asc']);
        $imports = $catalog->getImports();
        $this->checkOrder([$import3, $import1, $import2], $imports);
    }

    private function checkOrder(array $expectImports, array $actualImports): void {
        $this->assertCount(count($expectImports), $actualImports);
        foreach($expectImports as $index => $expectImport)
            $this->assertEquals($expectImports[$index]->getImportId(), $actualImports[$index]['id']);
    }

    private static function createImportWithDate(iStore $store, int $time): iImport {
        $import = self::getUniqueImport($store);
        $import->update(['import_date' => Date::getStringTimeStamp($time)]);
        return $import;
    }

    static function createCatalogFromFilter(array $filter = []): ImportsCatalog {
        return new ImportsCatalog(TestPragmaFactory::getPragmaAccountId(), $filter);
    }
}