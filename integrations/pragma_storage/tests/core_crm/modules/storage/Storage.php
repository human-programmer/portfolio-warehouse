<?php


namespace PragmaStorage\Test\Storage;


use Calculator\Test\TestCalculator;
use PragmaStorage\iExport;
use PragmaStorage\iStorage;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/ExportsDataSets.php';
require_once __DIR__ . '/../../../../../calculator/tests/modules_values/TestCalculator.php';

class Storage extends \PHPUnit\Framework\TestCase {
	use ExportsDataSets, TestCalculator;

	private iExport $current_export;
	private int $expected_entity_id;
	private int $expected_product_id;
	private float $expected_quantity;
	private float $expected_selling_price;
	private float $expected_total_selling_price;

	public static function setUpBeforeClass(): void {
		TestPragmaFactory::ifAmocrmInitTest();
		self::beforeCalcClass();
		parent::setUpBeforeClass();
	}

	public static function tearDownAfterClass(): void {
		self::clearExportDatasets();
		self::afterCalcClass();
		parent::tearDownAfterClass();
	}

	/**
	 * @dataProvider exportDataSets
	 */
	function testCreateExport (int $pragma_entity_id, int $product_id, float $quantity, float $selling_price){
		$this->setExpectedEntityId($pragma_entity_id);
		$this->setExpectedProductId($product_id);
		$this->setExpectedQuantity($quantity);
		$this->setExpectedSellingPrice($selling_price);
		$this->setExpectedTotalSellingPrice($selling_price * $quantity);

		$this->createExport();
		$this->checkExport();
		$this->checkChangedEntity();
		$this->checkCalculator();
		$this->checkDeleteExport();
	}

	private function createExport () : void {
		$this->current_export = self::getStorage()->createPragmaExport(
			$this->getExpectedEntityId(),
			$this->getExpectedProductId(),
			$this->getExpectedQuantity(),
			$this->getExpectedSellingPrice());
	}

	public function getCurrentExport(): iExport {
		return $this->current_export;
	}

	protected function checkExport() : void {
		$this->checkEntityId();
		$this->checkProductId();
		$this->checkQuantity();
		$this->checkSellingPrice();
	}

	private function checkEntityId() : void {
		$this->assertEquals($this->getExpectedEntityId(), $this->getCurrentExport()->getEntityId());
	}

	private function checkProductId() : void {
		$this->assertEquals($this->getExpectedProductId(), $this->getCurrentExport()->getProductId());
	}

	private function checkQuantity() : void {
		$this->assertEquals($this->getExpectedQuantity(), $this->getCurrentExport()->getQuantity());
	}

	private function checkSellingPrice() : void {
		$this->assertEquals($this->getExpectedSellingPrice(), $this->getCurrentExport()->getSellingPrice());
	}

	private function checkChangedEntity () : void {
		$updated_entities = self::getUpdatedEntities();
		foreach ($updated_entities as $updated_entity)
			if($this->getExpectedEntityId() === $updated_entity->getPragmaEntityId()) {
				$this->assertTrue(true);
				return;
			}
		$this->assertTrue(false);
	}

	private function checkDeleteExport () : void {
		self::getStorage()->deleteExport($this->getCurrentExport()->getExportId());
		$this->setExpectedQuantity(0);
		$this->setExpectedTotalSellingPrice(0);
		$this->checkCalculator();
	}

	private function checkCalculator() : void {
		$fields = [
			'storage_total_selling_price',
//			'storage_total_profit_price',
//			'storage_total_purchase_price',
		];
		foreach ($fields as $field_name)
			$this->checkValueCalculator($field_name);
	}

	private function checkValueCalculator(string $field_name) : void {
		$this->setExpectedCalcEntity($this->getExpectedEntityId());
		$this->setExpectedCalcFieldName($field_name);
		$this->setExpectedCalcFieldValue($this->getExpectedTotalSellingPrice());
		$this->checkCalcField();
	}

	static private function getStorage() : iStorage {
		return TestPragmaFactory::getTestStorage();
	}

	public function getExpectedEntityId(): int {
		return $this->expected_entity_id;
	}

	public function setExpectedEntityId(int $expected_entity_id): void {
		$this->expected_entity_id = $expected_entity_id;
	}

	public function getExpectedProductId(): int {
		return $this->expected_product_id;
	}

	public function setExpectedProductId(int $expected_product_id): void {
		$this->expected_product_id = $expected_product_id;
	}

	public function getExpectedQuantity(): float {
		return $this->expected_quantity;
	}

	public function setExpectedQuantity(float $expected_quantity): void {
		$this->expected_quantity = $expected_quantity;
	}

	public function getExpectedSellingPrice(): float {
		return $this->expected_selling_price;
	}

	public function setExpectedSellingPrice(float $expected_selling_price): void {
		$this->expected_selling_price = $expected_selling_price;
	}

	public function getExpectedTotalSellingPrice(): float {
		return $this->expected_total_selling_price;
	}

	public function setExpectedTotalSellingPrice(float $expected_total_selling_price): void {
		$this->expected_total_selling_price = $expected_total_selling_price;
	}
}