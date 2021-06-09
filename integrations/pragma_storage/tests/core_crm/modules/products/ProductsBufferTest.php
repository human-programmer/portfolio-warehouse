<?php


namespace PragmaStorage\Test;


use PragmaStorage\iProduct;
use PragmaStorage\TProductsBuffer;

require_once __DIR__ . '/../../TestPragmaFactory.php';
require_once __DIR__ . '/../../../../core_crm/modules/products/TProductsBuffer.php';

class ProductsBufferTest extends \PHPUnit\Framework\TestCase {
	use ProductsCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::init_test();
	}

	protected function tearDown(): void {
		parent::tearDown();
		self::clearProducts();
	}


	function testAddInBuffer(){
		$buffer = new TestProductsBuffer();
		$product_0 = self::getUniqueProduct();
		$this->assertCount(0, $buffer->getTestProducts());

		$buffer->addInBufferTest($product_0);
		$buffer->addInBufferTest($product_0);
		$this->assertCount(1, $buffer->getTestProducts());

		$buffer->addInBufferTest(self::getUniqueProduct());
		$this->assertCount(2, $buffer->getTestProducts());

		$buffer->addInBufferTest(self::getUniqueProduct());
		$this->assertCount(3, $buffer->getTestProducts());
	}

	function testDeleteFromBuffer(){
		$buffer = new TestProductsBuffer();
		$buffer->addInBufferTest(self::getUniqueProduct());
		$buffer->addInBufferTest(self::getUniqueProduct());
		$testProduct = self::getUniqueProduct();
		$buffer->addInBufferTest($testProduct);
		$this->assertCount(3, $buffer->getTestProducts());

		$buffer->deleteFromBufferTest($testProduct->getProductId());
		$this->assertCount(2, $buffer->getTestProducts());
	}

	function testFindInBuffer(){
		$buffer = new TestProductsBuffer();
		$testProduct = self::getUniqueProduct();
		$this->assertNull($buffer->findInBufferTest($testProduct->getProductId()));

		$buffer->addInBufferTest($testProduct);
		$this->assertInstanceOf(iProduct::class, $buffer->findInBufferTest($testProduct->getProductId()));

		$buffer->addInBufferTest(self::getUniqueProduct());
		$buffer->addInBufferTest(self::getUniqueProduct());
		$buffer->addInBufferTest(self::getUniqueProduct());
		$this->assertInstanceOf(iProduct::class, $buffer->findInBufferTest($testProduct->getProductId()));
	}
}

class TestProductsBuffer {
	use TProductsBuffer;

	function deleteFromBufferTest(int $product_id): void {
		$this->deleteFromBuffer($product_id);
	}
	function findInBufferTest(int $product_id): iProduct|null {
		return $this->findInBuffer($product_id);
	}
	function addInBufferTest(iProduct $product): void {
		$this->addInBuffer($product);
	}
	function getTestProducts(): array {
		return $this->products;
	}
}