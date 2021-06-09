<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImport;
use PragmaStorage\iStore;

require_once __DIR__ . '/../stores/StoresCreator.php';

trait ImportsCreator {
	use StoresCreator;

	static private array $imports = [];
	static private iStore $default_store;

	static function setDefaultStore(iStore $store) : void {
		self::$default_store = $store;
	}

	static function getUniqueImports (int $quantity) : array {
		for ($i = 0; $i < $quantity; ++$i)
			$result[] = self::getUniqueImport();
		return $result ?? [];
	}

	static function getUniqueImport (iStore $store = null) : iImport {
		$store = self::getStore($store);
		$import = AbstractCreator::getImports()->createImport($store, ['provider' => AbstractCreator::getUniqueString()]);
		self::$imports[] = $import;
		return $import;
	}

	static function getUniqueDeficitImport (iStore $store = null) : iImport {
		$store = self::getStore($store);
		$import = AbstractCreator::getImports()->getDeficitImport($store->getStoreId());
		self::$imports[] = $import;
		return $import;
	}

	static private function getStore ($store) : iStore {
		return $store ?? self::$default_store ?? self::getUniqueStore();
	}

	static function clearImports() : void {
		foreach (self::$imports as $import)
			$import->delete();
		self::$imports = [];
	}
}