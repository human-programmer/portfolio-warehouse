<?php


namespace PragmaStorage\Test;


use PragmaStorage\Product;
use PragmaStorage\Products;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class Products2Test extends \PHPUnit\Framework\TestCase {
	use ProductsCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	static function tearDownAfterClass(): void {
		parent::tearDownAfterClass();
		self::clear
		self::clearProducts();
	}

	/**
	 * @dataProvider instProducts
	 */
	function testPreloadProducts(TestProductsWrapper $productsStore){
		$expectProducts = self::createProducts();
		$this->assertCount(0, $productsStore->getProductsFromBuffer());
		$id = array_keys($expectProducts);
		$productsStore->preloadProducts($id);
		$this->assertCount(count($expectProducts), $productsStore->getProductsFromBuffer());
		foreach ($productsStore->getProductsFromBuffer() as $product)
			$this->assertInstanceOf(Product::class, $expectProducts[$product->getPRoductId()]);
	}

	static function createProducts(int $quantity = 10): array {
		TestPragmaFactory::ifInitTest();
		for ($i = 0; $i < 10; $i++) {
			$product = self::getUniqueProduct();
			$products[$product->getProductId()] = $product;
		}
		return $products;
	}

	static function instProducts(){
		TestPragmaFactory::ifInitTest();
		return[[new TestProductsWrapper(TestPragmaFactory::getStoreApp())]];
	}
}

class TestProductsWrapper extends Products {

}