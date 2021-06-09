<?php


namespace PragmaStorage\Test;


use PragmaStorage\iExport;
use PragmaStorage\iProduct;
use PragmaStorage\IStoreApp;
use PragmaStorage\ITravel;
use PragmaStorage\ITravelLink;
use PragmaStorage\StoreApp;
use PragmaStorage\Travel;
use const PragmaStorage\EXPORT_STATUS_RESERVED;

require_once __DIR__ . '/../../../../core_crm/modules/travels/travel/Travel.php';
require_once __DIR__ . '/TravelsTestPrepare.php';

class TravelTest extends \PHPUnit\Framework\TestCase {
	use TravelsTestPrepare;

	static function model(): array {
		return [
			[[
				'travel_id' => 123,
				'end_import_id' => 1234,
				'creation_date' => 12345,
				'start_store_id' => 123456,
				'end_store_id' => 1234567,
				'user_id' => 12345678,
				'travel_date' => 123456789,
				'travel_status' => EXPORT_STATUS_RESERVED,
			]]
		];
	}

	/**
	 * @dataProvider model
	 */
	function testCreate(array $model){
		$travel = new Travel(TestPragmaFactory::getStoreApp(), TestPragmaFactory::getStoreApp()->getTravels(), $model);
		$this->assertIsArray($model);
		$this->assertEquals($model['travel_id'], $travel->getTravelId());
		$this->assertEquals($model['end_import_id'], $travel->getEndImportId());
		$this->assertEquals($model['creation_date'], $travel->getCreationDate());
		$this->assertEquals($model['start_store_id'], $travel->getStartStoreId());
		$this->assertEquals($model['end_store_id'], $travel->getEndStoreId());
		$this->assertEquals($model['user_id'], $travel->getUserId());
		$this->assertEquals($model['travel_date'], $travel->getTravelDate());
		$this->assertEquals($model['travel_status'], $travel->getTravelStatus());
	}

	function testAddProduct(): void {
        $travel = self::uniqueTravel();
        $product = self::getUniqueProduct();
	    $travel->addProduct($product->getProductId(), 1000);
	    $this->checkTravelLink($travel, $product, 1000);

	    $travel->addProduct($product->getProductId(), 500);
	    $this->checkTravelLink($travel, $product, 500);

	    $travel->addProduct($product->getProductId(), 1500);
	    $this->checkTravelLink($travel, $product, 1500);
    }

    function testAddProducts(): void {
        $travel = self::uniqueTravel();
        $product1 = self::getUniqueProduct();
        $product2 = self::getUniqueProduct();
        $travel->addProducts([
            ['product_id' => $product1->getProductId(), 'quantity' => 200],
            ['product_id' => $product2->getProductId(), 'quantity' => 500]
        ]);
        $this->checkTravelLink($travel, $product1, 200);
        $this->checkTravelLink($travel, $product2, 500);

        $travel->addProducts([
            ['product_id' => $product1->getProductId(), 'quantity' => 100],
            ['product_id' => $product2->getProductId(), 'quantity' => 300]
        ]);
        $this->checkTravelLink($travel, $product1, 100);
        $this->checkTravelLink($travel, $product2, 300);

        $travel->addProducts([
            ['product_id' => $product1->getProductId(), 'quantity' => 1100],
            ['product_id' => $product2->getProductId(), 'quantity' => 1300]
        ]);
        $this->checkTravelLink($travel, $product1, 1100);
        $this->checkTravelLink($travel, $product2, 1300);

        $this->assertCount(2, $travel->getLinks());
    }

    private function checkTravelLink(ITravel $travel, iProduct $product, float $expect_quantity): void {
	    $expect_link = $travel->findTravelLink($product->getProductId());
	    $this->assertEquals($product->getProductId(), $expect_link->getProductId());
	    $this->assertEquals($travel->getTravelId(), $expect_link->getTravelId());

	    $this->assertEquals($product->getProductId(), $expect_link->findStartExport()->getProductId());
	    $this->checkExportsStore($expect_link->findStartExport(), $travel->getStartStoreId());
	    $this->checkQuantities($expect_link, $expect_quantity);

	    $this->checkActualTravelLink($expect_link);
    }

    private function checkExportsStore(iExport $export, int $expectStoreId): void {
	    $productsImports = $export->getProductsImports();
	    $this->assertTrue(count($productsImports) >= 1);
	    foreach($productsImports as $productImport)
	        $this->assertEquals($expectStoreId, $productImport->findStoreId());
    }

    private function checkQuantities(ITravelLink $link, float $expectQuantity):void {
	    $this->assertEquals($expectQuantity, $link->findStartExport()->getQuantity());
	    $this->assertEquals($expectQuantity, $link->findReceiveProductImport()->getImportQuantity());
	    $this->assertEquals($expectQuantity, $link->getQuantity());
    }

    private function checkActualTravelLink(ITravelLink $link) {
        $actual = self::createStoreApp()->getTravelLinks()->findTravelLink($link->getTravelId(), $link->getProductId());

        $this->assertEquals($link->getTravelId(), $actual->getTravelId());
        $this->assertEquals($link->getProductId(), $actual->getProductId());
        $this->assertEquals($link->getReceiveProductImportId(), $actual->getReceiveProductImportId());
        $this->assertEquals($link->getStartExportId(), $actual->getStartExportId());

        $this->checkQuantities($actual, $link->getQuantity());
    }

    private static function createStoreApp(): IStoreApp {
	    return new StoreApp(TestPragmaFactory::getPragmaAccountId());
    }
}