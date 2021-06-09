<?php


namespace PragmaStorage\Test;


use PragmaStorage\iCategories;
use PragmaStorage\iProducts;

trait ProductsDataSets {

	static private array $categories = [];

	function getProductsForCreate () : array {
		if(!TestPragmaFactory::isTestInit())
			TestPragmaFactory::init_test();

		$category_id_1 = self::getUniqueCategoryId();
		$category_id_2 = self::getUniqueCategoryId();

		return [
			[
				$category_id_1,
				self::getUniqueString(),
				'test_product_title',
				10.1,
				['unit' => 'm3']
			],
			[
				$category_id_1,
				self::getUniqueString(),
				'    test_product_title    ',
				10.1,
				['unit' => 'm3']
			],
			[
				$category_id_1,
				time() . '  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest  articletest   ',
				'    test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title        test_product_title    ',
				10.1,
				['unit' => 'm3']
			],
			[
				$category_id_2,
				self::getUniqueString(),
				'',
				10.1,
				['unit' => 'm3']
			],
			[
				$category_id_2,
				self::getUniqueString(),
				'test_product_title',
				10.1,
				[]
			],
		];
	}

	static protected function getSetsForUpdateVarchar () : array {
		return [
			[
				'      ',
			],
			[
				'',
			],
			[
				self::getUniqueString() . 'testtesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttesttest',
			],
			[
				'    ' . self::getUniqueString() . '    ',
			],
		];
	}


	static protected function getFloatValues () : array {
		return [
			[1.1111],
			[11111.12312312312313],
			[1.1],
			[1],
		];
	}

	static protected function getUniqueCategoryId () : int {
		$category = self::getCategories()->createCategory(uniqid('test_products'));
		self::$categories[] = $category;
		return $category->getCategoryId();
	}

	static private function getUniqueString() : string {
		return uniqid('product');
	}

	static protected function getCategories() : iCategories {
		return TestPragmaFactory::getTestCategories();
	}

	static protected function getProducts() : iProducts {
		return TestPragmaFactory::getTestProducts();
	}

	static protected function clearDataSets() : void {
		foreach (self::$categories as $category)
			$category->delete();
	}

	static protected function formattingAsVarchar (string $string) : string {
		return trim(substr(trim($string), 0, 256));
	}
}