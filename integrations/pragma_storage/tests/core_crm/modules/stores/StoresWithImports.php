<?php


namespace PragmaStorage\Test;


use PragmaStorage\iCategory;
use PragmaStorage\iImport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\iStore;

require_once __DIR__ . '/../product_imports/ProductImportsCreator.php';

trait StoresWithImports {
	use ProductImportsCreator;

	static private int $default_store_quantity = 2;

	static private array $products = [];
	static private array $categories = [];

	static function getStoresWithImports () : array {
		self::setUniqueProductsForStore();
		$stores = self::getUniqueStores(self::getDefaultStoreQuantity());

		foreach ($stores as $store)
			$result[] = self::getStoreWithImports($store);
		return $result;
	}

	static function getStoreWithImports(iStore $store) : array {
		$products = self::getProductsForStore();
		$imports = self::getUniqueImportsForStore($store);

		foreach ($imports as $import)
			foreach ($products as $product)
				$productImports[] = self::getUniqueProductImportForStore($import, $product);

		return ['store' => $store, 'imports' => $imports, 'product_imports' => $productImports];
	}

	static private function getUniqueImportsForStore (iStore $store) : array {
		self::setDefaultStore($store);
		return self::getUniqueImports(4);
	}

	static private function setUniqueProductsForStore () : void {
		$category = self::getCategoryForStore();
		self::setDefaultCategory($category);
		self::$products = self::getUniqueProducts(4);
	}

	static function getCategoryForStore() : iCategory {
		$category = self::getUniqueCategory();
		self::$categories[] = $category;
		return $category;
	}

	static private function getProductsForStore() : array {
		return self::$products;
	}

	static private function getUniqueProductImportForStore (iImport $import, iProduct $product) : iProductImport {
		self::setDefaultImport($import);
		self::setDefaultProduct($product);
		self::setDefaultPurchasePrice(1000);
		self::setDefaultPimportQuantity(1000);

		return self::getUniqueProductImport();
	}

	static function clearStoresWithImports() : void{
		self::clearProductImports();
		self::clearImports();
//		self::clearProducts();
		self::clearCategories();
		self::clearStores();
	}

	static function clearProducts() : void {
		foreach (self::$products as $product)
			$product->delete();
		self::$products = [];
	}
	static function clearCategories(): void {
		foreach (self::$categories as $category)
			$category->delete();
		self::$categories = [];
	}

	public static function getDefaultStoreQuantity(): int {
		return self::$default_store_quantity;
	}

	public static function setDefaultStoreQuantity(int $default_store_quantity): void {
		self::$default_store_quantity = $default_store_quantity;
	}
}