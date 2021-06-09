<?php


namespace PragmaStorage\Test;


use PragmaStorage\iStore;


trait StoresCreator {
	static private array $stores = [];

	static function getUniqueStore() : iStore {
		$store = AbstractCreator::getStores()->createStore(AbstractCreator::getUniqueString(), AbstractCreator::getUniqueString());
		self::$stores[] = $store;
		return $store;
	}

	static function getUniqueStores (int $quantity) : array {
		for ($i = 0; $i < $quantity; ++$i)
			$result[] = self::getUniqueStore();
		return $result ?? [];
	}

	static function clearStores() : void {
//		foreach (self::$stores as $store)
//			$store->delete();
//		self::$stores = [];
	}
}