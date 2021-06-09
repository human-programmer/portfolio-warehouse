<?php


namespace PragmaStorage\Test;


use Generals\CRMDB;
use PragmaStorage\iCategory;
use PragmaStorage\iProduct;
use PragmaStorage\iStore;

require_once __DIR__ . '/../categories/CategoriesCreator.php';

trait ProductsCreator {
	use CategoriesCreator;

	static private array $products = [];
	static private iCategory $default_category;

	static function setDefaultCategory(iCategory $category) : void {
		self::$default_category = $category;
	}

	static function getUniqueProductForStore(array $store_id = null): iProduct {
	    $category = self::getUniqueCategory($store_id);
        $product = self::_createTestProduct($category);
        self::$products[] = $product;
        return $product;
    }

	static function getUniqueProduct(iCategory $category = null) : iProduct {
		$category = self::getCategory($category);
		$product = self::_createTestProduct($category);
		self::$products[] = $product;
		return $product;
	}

	static function getUniqueProducts (int $quantity) : array {
		for ($i = 0; $i < $quantity; ++$i)
			$result[] = self::getUniqueProduct();
		return $result ?? [];
	}

	static private function getCategory ($category) : iCategory {
		return $category ?? self::$default_category ?? self::getUniqueCategory();
	}

	static private function _createTestProduct (iCategory $category) : iProduct {
		return AbstractCreator::getProducts()->createProduct($category->getCategoryId(),AbstractCreator::getUniqueString(),AbstractCreator::getUniqueString(),10);
	}

	static function clearProducts() : void {
		foreach (self::$products as $product)
			$product->delete();
		self::$products = [];
		self::clearCategories();
	}

}