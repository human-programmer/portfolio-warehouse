<?php


namespace PragmaStorage\Test;


use PragmaStorage\iCategory;
use PragmaStorage\iStore;

trait CategoriesCreator {
	use StoresCreator;
	static private array $categories = [];

	static function getUniqueCategory (array $stores_id = null) : iCategory {
		$stores_id = $stores_id ? $stores_id : self::getAllTestStoresId();
		$category = AbstractCreator::getCategories()->createCategory(AbstractCreator::getUniqueString(), $stores_id);
		self::$categories[] = $category;
		return $category;
	}

	static function getAllTestStoresId(): array {
		$stores = TestPragmaFactory::getStores()->getStores();
		if(!count($stores))
			$stores = [self::getUniqueStore()];
		foreach ($stores as $store)
			$res[] = $store->getStoreId();
		return $res;
	}

	static function clearCategories() : void {
		foreach (self::$categories as $category)
			$category->delete();
		self::$categories = [];
	}
}