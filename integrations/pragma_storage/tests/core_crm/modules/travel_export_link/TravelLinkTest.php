<?php


namespace PragmaStorage;


use PragmaStorage\Test\ExportsCreator;
use PragmaStorage\Test\TestPragmaFactory;
use PragmaStorage\Test\TravelCreator;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class TravelLinkTest extends \PHPUnit\Framework\TestCase {
    use ExportsCreator, TravelCreator;

    static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        TestPragmaFactory::ifInitTest();
        TestPragmaFactory::resetStoreApp();
    }

    function testCreate(): void {
        $store_app = TestPragmaFactory::getStoreApp();
        $travel = self::uniqueTravel();
        $product = self::getUniqueProduct();
        $quantity = 100;
        $link = new TravelLink($store_app, ['travel_id' => $travel->getTravelId(), 'product_id' => $product->getProductId(), 'quantity' => $quantity]);
        $this->checkTravelLink($link, $travel, $product, $quantity, null, null);
        $link->updateLinks();
        $this->checkActualLink($link);
        $expect_export = TestPragmaFactory::getExports()->getExport($link->getStartExportId());
        $expect_import = TestPragmaFactory::getProductImports()->getProductImport($link->getReceiveProductImportId());
        $this->assertInstanceOf(iExport::class, $expect_export);
        $this->assertInstanceOf(iProductImport::class, $expect_import);
        $this->checkTravelLink($link, $travel, $product, $quantity, $expect_export->getExportId(), $expect_import->getProductImportId());

        $quantity = 60;
        $link->setQuantity($quantity);
        $this->checkTravelLink($link, $travel, $product, $quantity, $expect_export->getExportId(), $expect_import->getProductImportId());

        $quantity = 120;
        $link->setQuantity($quantity);
        $this->checkTravelLink($link, $travel, $product, $quantity, $expect_export->getExportId(), $expect_import->getProductImportId());
    }

    private function checkTravelLink(ITravelLink $link, ITravel $travel, iProduct $product, float $quantity, int|null $export_id, int|null $product_import_id): void {
        $this->assertEquals($travel->getTravelId(), $link->getTravelId());
        $this->assertEquals($product->getProductId(), $link->getProductId());
        $this->assertEquals($quantity, $link->getQuantity());
        $this->assertEquals($export_id, $link->getStartExportId());
        $this->assertEquals($product_import_id, $link->getReceiveProductImportId());

        if($export_id){
            $export = TestPragmaFactory::getExports()->getExport($export_id);
            $this->assertEquals($quantity, $export->getQuantity());
            $this->assertEquals(EXPORT_STATUS_EXPORTED, $export->getStatusId());
            $this->checkExportStore($export, $travel);
        }
        if($product_import_id) {
            $import = TestPragmaFactory::getTestProductImports()->getProductImport($product_import_id);
            $this->assertEquals($travel->getEndStoreId(), $import->findStoreId());
            $this->assertEquals($quantity, $import->getImportQuantity());
        }
    }

    private function checkExportStore(iExport $export, ITravel $travel): void {
        $productsImports = $export->getProductsImports();
        foreach($productsImports as $productImport){
            $id = $productImport->findStoreId();
            $this->assertEquals($travel->getStartStoreId(), $productImport->findStoreId());
        }
    }

    private function checkActualLink(ITravelLink $link): void {
        $links = new TravelsLinks(new StoreApp(TestPragmaFactory::getPragmaAccountId()));
        $actualLink = $links->findTravelLink($link->getTravelId(), $link->getProductId());
        $this->assertFalse($link === $actualLink);
        $this->compareLinks($link, $actualLink);
    }

    private function compareLinks(ITravelLink $expect, ITravelLink $actual): void {
        $this->assertEquals($expect->getTravelId(), $actual->getTravelId());
        $this->assertEquals($expect->getProductId(), $actual->getProductId());
        $this->assertEquals($expect->getStartExportId(), $actual->getStartExportId());
        $this->assertEquals($expect->getReceiveProductImportId(), $actual->getReceiveProductImportId());
        $this->assertEquals($expect->getQuantity(), $actual->getQuantity());

        if($expect->getStartExportId()) {
            $expect_export = $expect->findStartExport();
            $actual_export = $actual->findStartExport();

            $this->assertFalse($expect_export === $actual_export);

            $this->assertEquals($expect_export->getExportId(), $actual_export->getExportId());
            $this->assertEquals($expect_export->getQuantity(), $actual_export->getQuantity());
            $this->assertEquals($expect_export->getStatusId(), $actual_export->getStatusId());
            $this->assertEquals($expect_export->getSellingPrice(), $actual_export->getSellingPrice());
            $this->assertEquals($expect_export->getClientType(), $actual_export->getClientType());
        }

        if($expect->getReceiveProductImportId()) {
            $expect_import = $expect->findReceiveProductImport();
            $actual_import = $actual->findReceiveProductImport();

            $this->assertFalse($expect_import === $actual_import);

            $this->assertTrue($expect_import->getProductImportId() === $actual_import->getProductImportId());

            $this->assertEquals($expect_import->getProductImportId(), $actual_import->getProductImportId());
            $this->assertEquals($expect_import->getProductId(), $actual_import->getProductId());

            $this->assertEquals($expect_import->getImportId(), $actual_import->getImportId());
            $this->assertEquals($expect_import->getImportQuantity(), $actual_import->getImportQuantity());
            $this->assertEquals($expect_import->getPurchasePrice(), $actual_import->getPurchasePrice());

            $this->assertEquals($expect_import->getSource(), $actual_import->getSource());
            $this->assertEquals($expect_import->isDeficit(), $actual_import->isDeficit());
            $this->assertEquals($expect_import->getFreeBalanceQuantity(), $actual_import->getFreeBalanceQuantity());

            $this->assertEquals($expect_import->getBalanceQuantity(), $actual_import->getBalanceQuantity());
            $this->assertEquals($expect_import->getImportDate(), $actual_import->getImportDate());
            $this->assertEquals($expect_import->getDateCreate(), $actual_import->getDateCreate());

            $this->assertEquals($expect->getTravel()->getEndStoreId(), $actual_import->findStoreId());
        }
    }

    function testUpdatePurchasePrice(): void {
        $store_app = TestPragmaFactory::getStoreApp();
        $travel = self::uniqueTravel();
        $start_store = $store_app->getStores()->getStore($travel->getStartStoreId());
        $end_store = $store_app->getStores()->getStore($travel->getEndStoreId());
        $product = self::getUniqueProductForStore([$start_store->getStoreId(), $end_store->getStoreId()]);

        $productImport1 = self::createUniqueProductImport($start_store, $product, 1, 10);
        $productImport2 = self::createUniqueProductImport($start_store, $product, 10, 100);
        $productImport3 = self::createUniqueProductImport($start_store, $product, 100, 1000);
        $total_quantity = 111;
        $total_purchase_price = 1 * 10 + 10 * 100 + 100 * 1000;
        $spec_cost = $total_purchase_price / $total_quantity;

        $link = new TravelLink($store_app, ['travel_id' => $travel->getTravelId(), 'product_id' => $product->getProductId(), 'quantity' => $total_quantity]);
        $link->updateLinks();
        $this->checkTotalPurchasePrice($link, $total_purchase_price, $total_quantity);
    }

    private function checkTotalPurchasePrice(ITravelLink $link, float $expect_purchase_price, float $total_quantity): void {
        $this->assertEquals($expect_purchase_price, $link->getTotalPurchasePrice());
        $this->assertEquals($expect_purchase_price / $total_quantity, $link->findReceiveProductImport()->getPurchasePrice());
    }

    private static function createUniqueProductImport(iStore $store, iProduct $product, float $quantity, float $purchasePrice): iProductImport {
        self::setDefaultPimportQuantity($quantity);
        self::setDefaultPurchasePrice($purchasePrice);
        return self::getUniqueProductImportForStore($product, $store);
    }

    static function createTravelLinks(): ITravelLinks {
        $storeApp = self::storeApp();
        return new TravelsLinks($storeApp);
    }

    static function storeApp(): IStoreApp {
        return new StoreApp(TestPragmaFactory::getPragmaAccountId());
    }
}