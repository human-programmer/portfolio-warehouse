<?php


namespace PragmaStorage;


use PragmaStorage\Test\ProductsCreator;
use PragmaStorage\Test\StoresCreator;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../TestPragmaFactory.php';

class CategoriesToStoresTest extends \PHPUnit\Framework\TestCase {
	use StoresCreator, ProductsCreator;

	static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		TestPragmaFactory::ifInitTest();
	}

	function testGetStoresForcategory(): void {
		$categoriesToStores = self::categoriesToStores();
		$category1 = self::uniqueCategory();
		$category2 = self::uniqueCategory();

		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();
		$store4 = self::getUniqueStore();

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store1->getStoreId()]);
		$actual = $categoriesToStores->getStoresForCategory($category1->getCategoryId());

		$this->assertCount(1, $actual);
		$this->assertEquals($store1->getStoreId(), $actual[0]);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store2->getStoreId()]);
		$actual = $categoriesToStores->getStoresForCategory($category1->getCategoryId());

		$this->assertCount(2, $actual);
	}

	function testGetCategoriesIdInStore(): void {
		$categoriesToStores = self::categoriesToStores();
		$category1 = self::uniqueCategory();
		$category2 = self::uniqueCategory();
		$category3 = self::uniqueCategory();

		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();

		$this->checkGetCategoriesForStore($categoriesToStores, $store1->getStoreId(), []);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store1->getStoreId()]);
		$this->checkGetCategoriesForStore($categoriesToStores, $store1->getStoreId(), [$category1->getCategoryId()]);

		$categoriesToStores->saveCategoryLinks($category2->getCategoryId(), [$store1->getStoreId()]);
		$this->checkGetCategoriesForStore($categoriesToStores, $store1->getStoreId(), [$category1->getCategoryId(), $category2->getCategoryId()]);

		$categoriesToStores->saveCategoryLinks($category3->getCategoryId(), [$store1->getStoreId(), $store2->getStoreId()]);
		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store2->getStoreId()]);
		$this->checkGetCategoriesForStore($categoriesToStores, $store2->getStoreId(), [$category1->getCategoryId(), $category3->getCategoryId()]);

	}

	function testGetCategoryIdOfProduct(): void {
		$categoriesToStores = self::categoriesToStores();
		$category1 = self::uniqueCategory();
		$category2 = self::uniqueCategory();

		$product1_1 = self::getUniqueProduct($category1);
		$product1_2 = self::getUniqueProduct($category1);

		$this->checkCategoryOfProduct($categoriesToStores, $product1_1, $category1);
		$this->checkCategoryOfProduct($categoriesToStores, $product1_2, $category1);

		$product2_1 = self::getUniqueProduct($category2);
		$product2_2 = self::getUniqueProduct($category2);

		$this->checkCategoryOfProduct($categoriesToStores, $product2_1, $category2);
		$this->checkCategoryOfProduct($categoriesToStores, $product2_2, $category2);
	}

	function testGetProductsIdOfCategory(): void {
		$categoriesToStores = self::categoriesToStores();
		$products = self::products();
		$products->addHandler($categoriesToStores->getProductCreationHandler());

		$category1 = self::uniqueCategory();
		$category2 = self::uniqueCategory();

		$this->checkProductsIdOfCategory($categoriesToStores, $category1->getCategoryId(), []);
		$this->checkProductsIdOfCategory($categoriesToStores, $category2->getCategoryId(), []);

		$product1_1 = self::createUniqueProduct($products, $category1);
		$product1_2 = self::createUniqueProduct($products, $category1);
		$product1_3 = self::createUniqueProduct($products, $category1);

		$this->checkProductsIdOfCategory($categoriesToStores, $category1->getCategoryId(), [$product1_1->getProductId(),$product1_2->getProductId(),$product1_3->getProductId(),]);
		$this->checkProductsIdOfCategory($categoriesToStores, $category2->getCategoryId(), []);
	}

	function testGetProductsIdInStore(): void {
		$categoriesToStores = self::categoriesToStores();
		$products = self::products();
		$products->addHandler($categoriesToStores->getProductCreationHandler());

		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();

		$category1 = self::uniqueCategory();
		$category2 = self::uniqueCategory();

		$product1_1 = self::createUniqueProduct($products, $category1);
		$product1_2 = self::createUniqueProduct($products, $category1);
		$product1_3 = self::createUniqueProduct($products, $category1);

		$product2_2 = self::createUniqueProduct($products, $category2);
		$product2_3 = self::createUniqueProduct($products, $category2);

		$this->checkProductsIdInStore($categoriesToStores, $store1->getStoreId(), []);
		$this->checkProductsIdInStore($categoriesToStores, $store2->getStoreId(), []);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store1->getStoreId()]);

		$expect = [$product1_1->getProductId(), $product1_2->getProductId(), $product1_3->getProductId()];
		$this->checkProductsIdInStore($categoriesToStores, $store1->getStoreId(), $expect);
		$this->checkProductsIdInStore($categoriesToStores, $store2->getStoreId(), []);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store1->getStoreId()]);
		$this->checkProductsIdInStore($categoriesToStores, $store1->getStoreId(), $expect);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store2->getStoreId()]);

		$this->checkProductsIdInStore($categoriesToStores, $store1->getStoreId(), $expect);
		$this->checkProductsIdInStore($categoriesToStores, $store2->getStoreId(), $expect);
	}

	function testGetStoresIdForProductError(): void {
		$categoriesToStores = self::categoriesToStores();
		$products = self::products();
		$products->addHandler($categoriesToStores->getProductCreationHandler());

		$category1 = self::uniqueCategory();

		$product1_1 = self::createUniqueProduct($products, $category1);

		$this->expectException(\Exception::class);
		$this->checkStoresIdForProduct($categoriesToStores, $product1_1->getProductId(), []);
	}

	function testGetStoresIdForProduct(): void {
		$categoriesToStores = self::categoriesToStores();
		$products = self::products();
		$products->addHandler($categoriesToStores->getProductCreationHandler());

		$store1 = self::getUniqueStore();
		$store2 = self::getUniqueStore();
		$store3 = self::getUniqueStore();

		$category1 = self::uniqueCategory();

		$product1_1 = self::createUniqueProduct($products, $category1);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store1->getStoreId()]);
		$this->checkStoresIdForProduct($categoriesToStores, $product1_1->getProductId(), [$store1->getStoreId()]);

		$categoriesToStores->saveCategoryLinks($category1->getCategoryId(), [$store2->getStoreId(), $store3->getStoreId()]);
		$this->checkStoresIdForProduct($categoriesToStores, $product1_1->getProductId(), [$store1->getStoreId(),$store2->getStoreId(),$store3->getStoreId(),]);
	}

	private function checkStoresIdForProduct(ICategoriesToStores $links, int $product_id, array $expect): void {
		$this->checkStoresIdForProductTarget($links, $product_id, $expect);
		$links2 = self::categoriesToStores();
		$this->checkStoresIdForProductTarget($links2, $product_id, $expect);
	}

	private function checkStoresIdForProductTarget(ICategoriesToStores $links, int $product_id, array $expect): void {
		$actual = $links->getStoresIdForProduct($product_id);
		$this->assertCount(count($expect), $actual);

		foreach ($expect as $id)
			$this->assertFalse(array_search($id, $actual) === false);
	}

	private function checkProductsIdInStore(ICategoriesToStores $links, int $store_id, array $expect): void {
		$this->checkProductsIdInStoreTarget($links, $store_id, $expect);
		$links2 = self::categoriesToStores();
		$this->checkProductsIdInStoreTarget($links2, $store_id, $expect);
	}

	private function checkProductsIdInStoreTarget(ICategoriesToStores $links, int $store_id, array $expect): void {
		$actual = $links->getProductsIdInStore($store_id);
		$this->assertCount(count($expect), $actual);

		foreach ($expect as $id)
			$this->assertFalse(array_search($id, $actual) === false);
	}

	private function checkProductsIdOfCategory(ICategoriesToStores $links, int $category_id, array $expect): void {
		$this->checkProductsIdOfCategoryTarget($links, $category_id, $expect);
		$links2 = self::categoriesToStores();
		$this->checkProductsIdOfCategoryTarget($links2, $category_id, $expect);
	}

	private function checkProductsIdOfCategoryTarget(ICategoriesToStores $links, int $category_id, array $expect): void {
		$actual = $links->getProductsIdOfCategory($category_id);
		$this->assertCount(count($expect), $actual);

		foreach ($expect as $id)
			$this->assertFalse(array_search($id, $actual) === false);
	}

	static function createUniqueProduct(iProducts $products, iCategory $category): iProduct {
		return $products->createProduct(
			$category->getCategoryId(),
			uniqid('prod'),
			'titler',
			10
		);
	}

	private function checkCategoryOfProduct(ICategoriesToStores $links, iProduct $product, iCategory $expect_category): void {
		$actual = $links->getCategoryIdOfProduct($product->getProductId());
		$this->assertEquals($expect_category->getCategoryId(), $actual);

		$actual = self::categoriesToStores()->getCategoryIdOfProduct($product->getProductId());
		$this->assertEquals($expect_category->getCategoryId(), $actual);
	}

	private function checkGetCategoriesForStore(CategoriesToStores $current, int $store_id, array $expect_categories): void {
		$actual = self::categoriesToStores();
		$this->checkGetCategoriesForStoreTarget($current, $store_id, $expect_categories);
		$this->checkGetCategoriesForStoreTarget($actual, $store_id, $expect_categories);
	}

	private function checkGetCategoriesForStoreTarget(CategoriesToStores $categoriesToStores, int $store_id, array $expect_categories): void {
		$actual = $categoriesToStores->getCategoriesIdInStore($store_id);
		$this->assertCount(count($expect_categories), $actual);

		foreach ($expect_categories as $id)
			$this->assertFalse(array_search($id, $actual) === false);
	}

	static function products(): Products {
		return new Products(TestPragmaFactory::getStoreApp());
	}

	static function categoriesToStores(): CategoriesToStores {
		return new CategoriesToStores(TestPragmaFactory::getStoreApp());
	}

	static function uniqueCategory(): iCategory {
		$creator = new CategoryCreator(TestPragmaFactory::getPragmaAccountId());
		return $creator->uniqueCategory();
	}
}

class CategoryCreator extends PragmaStoreDB {
	function __construct(private int $account_id) {
		parent::__construct();
	}

	function uniqueCategory(): iCategory {
		$id = $this->categoryId();
		return new TestCategory($id);
	}

	private function categoryId(): int {
		$categories_schema = parent::getStorageCategoriesSchema();
		$sql = "INSERT INTO $categories_schema (`account_id`, `title`)
                VALUES ($this->account_id, 'sdfsdf')";
		self::executeSql($sql);
		return self::last_id();
	}
}

class TestCategory implements iCategory {
	public function __construct(private int $id) {
	}

	function getCategoryId(): int {
		return $this->id;
	}

	function getTitle(): string {
		return '';
	}

	function linkedStoreId(): array {
		// TODO: Implement linkedStoreId() method.
	}

	function isDeleted(): bool {
		// TODO: Implement isDeleted() method.
	}

	function delete(): bool {
		// TODO: Implement delete() method.
	}

	function recover() {
		// TODO: Implement recover() method.
	}

	function toArray(): array {
		// TODO: Implement toArray() method.
	}

	function update(array $model): bool {
		// TODO: Implement update() method.
	}
}