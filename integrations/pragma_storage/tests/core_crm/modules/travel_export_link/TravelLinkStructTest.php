<?php


namespace PragmaStorage;


use PragmaStorage\Test\ExportsCreator;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class TravelLinkStructTest extends \PHPUnit\Framework\TestCase {
    use ExportsCreator;

    static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        TestPragmaFactory::ifInitTest();
        TestPragmaFactory::resetStoreApp();
    }

    function testCreate(): void {
        $model1 = self::linkModel();
        $struct1 = new TravelLinkStruct($model1);
        $this->checkTravelLinkStruct($struct1, $model1);

        $model2 = self::linkModel();
        $struct2 = new TravelLinkStruct($model2);
        $this->checkTravelLinkStruct($struct2, $model2);

        $model3 = self::linkModel();
        $struct3 = new TravelLinkStruct($model3);
        $this->checkTravelLinkStruct($struct3, $model3);
    }

    function testSetExportId(): void {
        $export1 = self::getUniqueExport();
        $export2 = self::getUniqueExport();

        $model1 = self::linkModel(['product_id' => $export1->getProductId()]);
        $struct1 = new TravelLinkStruct($model1);
        $this->assertNull($struct1->getStartExportId());

        $struct1->setStartExportId($export1->getExportId());
        $this->assertEquals($export1->getExportId(), $struct1->getStartExportId());

        $struct1->setStartExportId($export1->getExportId());
        $this->assertEquals($export1->getExportId(), $struct1->getStartExportId());


        $model1['send_export_id'] = $export1->getExportId();
        $this->checkTravelLinkStruct($struct1, $model1);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Travel link already exists');
        $struct1->setStartExportId($export2->getExportId());
    }

    function testSetExportIdInvalidProductId(): void {
        $export1 = self::getUniqueExport();
        $export2 = self::getUniqueExport();
        $this->assertNotEquals($export1->getProductId(), $export2->getProductId());

        $model1 = self::linkModel(['product_id' => $export1->getProductId()]);
        $struct1 = new TravelLinkStruct($model1);
        $this->assertNull($struct1->getStartExportId());

        $this->expectException(\Exception::class);
        $struct1->setStartExportId($export2->getExportId());
    }

    function testSetProductImportId(): void {
        $productImportId1 = self::getUniqueProductImport();
        $productImportId2 = self::getUniqueProductImport();
        $this->assertNotEquals($productImportId1->getProductId(), $productImportId2->getProductId());

        $model1 = self::linkModel(['product_id' => $productImportId1->getProductId()]);
        $struct1 = new TravelLinkStruct($model1);
        $this->assertNull($struct1->getStartExportId());

        $struct1->setReceiveProductImportId($productImportId1->getProductImportId());
        $model1['receive_product_import_id'] = $productImportId1->getProductImportId();
        $this->checkTravelLinkStruct($struct1, $model1);

        $struct1->setReceiveProductImportId($productImportId1->getProductImportId());
        $this->checkTravelLinkStruct($struct1, $model1);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Travel link already exists');
        $struct1->setReceiveProductImportId($productImportId2->getProductImportId());
    }

    function testSetProductImportIdInvalidProductId(): void {
        $productImportId1 = self::getUniqueProductImport();
        $productImportId2 = self::getUniqueProductImport();
        $this->assertNotEquals($productImportId1->getProductId(), $productImportId2->getProductId());

        $model1 = self::linkModel(['product_id' => $productImportId1->getProductId()]);
        $struct1 = new TravelLinkStruct($model1);
        $this->assertNull($struct1->getStartExportId());

        $this->expectException(\Exception::class);
        $struct1->setReceiveProductImportId($productImportId2->getProductImportId());
    }

    function testSetQuantity(): void {
        $model1 = self::linkModel();
        $struct1 = new TravelLinkStruct($model1);

        $this->checkTravelLinkStruct($struct1, $model1);
        $quantity = rand(1, 9999999999) / 3.0;
        $model1['quantity'] = $quantity;
        $struct1->setQuantity($quantity);
        $this->checkTravelLinkStruct($struct1, $model1);
        $this->assertEquals($quantity, $struct1->getQuantity());
    }

    private function checkTravelLinkStruct(ITravelLinkStruct $struct, array $expect_model): void {
        $this->assertEquals($expect_model['travel_id'], $struct->getTravelId());
        $this->assertEquals($expect_model['product_id'], $struct->getProductId());
        $this->assertEquals($expect_model['send_export_id'], $struct->getStartExportId());
        $this->assertEquals($expect_model['receive_product_import_id'], $struct->getReceiveProductImportId());
        $this->assertEquals($expect_model['quantity'], $struct->getQuantity());
    }

    private static function linkModel(array $model = []): array {
        $model['travel_id'] = isset($model['travel_id']) ? $model['travel_id'] : rand(1, 9999999999);
        $model['product_id'] = isset($model['product_id']) ? $model['product_id'] : rand(1, 9999999999);
        $model['quantity'] = isset($model['quantity']) ? $model['quantity'] : 0;
        $model['receive_product_import_id'] = isset($model['receive_product_import_id']) ? $model['receive_product_import_id'] : null;
        $model['send_export_id'] = isset($model['send_export_id']) ? $model['send_export_id'] : null;
        return $model;
    }
}