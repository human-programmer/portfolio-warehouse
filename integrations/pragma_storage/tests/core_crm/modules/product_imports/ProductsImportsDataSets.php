<?php


namespace PragmaStorage\Test;


use PragmaStorage\iExport;
use PragmaStorage\iProduct;

require_once __DIR__ . '/../exports/ExportsCreator.php';

trait ProductsImportsDataSets {
	use ExportsCreator;

	static private int $default_sets_quantity = 2;

	static function productImportsDataSets() : array {
		TestPragmaFactory::ifInitTest();
		TestPragmaFactory::resetStoreApp();
		for ($i = 0; $i < self::getDefaultSetsQuantity(); ++$i)
			$result[] = self::getProductImportDataSet();
		return $result ?? [];
	}

	static function deficitDataSets () : array {
		TestPragmaFactory::ifInitTest();
		for ($i = 0; $i < self::getDefaultSetsQuantity(); ++$i)
			$result[] = self::getDeficitDataSet(self::getUniqueProduct());
		return $result ?? [];
	}

	static function getDeficitDataSet(iProduct $product) : array {
		self::setDefaultPimportQuantity(1000);
		self::setDefaultPurchasePrice(100);
		return [
			'deficit' => self::getUniqueProductDeficit($product),
			'product' => $product,
			'deficit_quantity' => 1000,
			'purchase_price' => 100,
		];
	}

	static function getProductImportDataSet() : array {
		self::setDefaultPimportQuantity(1000);
		self::setDefaultPurchasePrice(100);
		$product = self::getUniqueProduct();
		$import = self::getUniqueImport();
		return [
			'deficit' => self::getUniqueProductImport($import, $product),
			'import' => $import,
			'product' => $product,
			'deficit_quantity' => 1000,
			'purchase_price' => 100,
		];
	}

	static protected function createExportForProduct (iProduct $product) : iExport {
		self::setDefaultSellingPrice(100);
		self::setDefaultProduct($product);
		return self::getUniqueExport();
	}

	public static function getDefaultSetsQuantity(): int {
		return self::$default_sets_quantity;
	}
}