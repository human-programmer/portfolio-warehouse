<?php


namespace PragmaStorage\Test\Storage;


use PragmaStorage\Test\EntitiesCreator;
use PragmaStorage\Test\ProductsCreator;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../entities/EntitiesCreator.php';
require_once __DIR__ . '/../products/ProductsCreator.php';

trait ExportsDataSets {
	use EntitiesCreator, ProductsCreator;

	static private array $entities = [];
	static private array $products = [];

	static function exportDataSets () : array {
		return [
			self::exportDataSet(),
			self::exportDataSet(),
			self::exportDataSet(),
		];
	}

	static function exportDataSet () : array {
		TestPragmaFactory::ifAmocrmInitTest();
		return [
			'pragma_entity_id' => self::getUniqueEntityId(),
			'product_id' => self::getUniqueProductId(),
			'quantity' => 100,
			'selling_price' => 6
		];
	}

	static private function getUniqueEntityId() : int {
		$entity = self::getUniqueEntity();
		self::$entities[] = $entity;
		return $entity->getPragmaEntityId();
	}

	static private function getUniqueProductId() : int {
		$product = self::getUniqueProduct();
		self::$products[] = $product;
		return $product->getProductId();
	}

	static protected function getEntities() : array {
		return self::$entities;
	}

	static protected function getProducts() : array {
		return self::$products;
	}

	static function clearExportDatasets() : void {
		self::clearProducts();
		self::clearEntities();
	}

	static private function getUpdatedEntities () : array {
		return TestPragmaFactory::getCRMEntitiesStorage()->getUpdatedEntities();
	}

	static private function getChangedValues() : array {
		return TestPragmaFactory::getCRMValuesStorage()->getChangedValues();
	}
}