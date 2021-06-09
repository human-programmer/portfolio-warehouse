<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImport;
use PragmaStorage\iProduct;
use PragmaStorage\iProductImport;
use PragmaStorage\IProductImportModel;
use PragmaStorage\iStore;
use PragmaStorage\ProductImportModel;

require_once __DIR__ . '/../imports/ImportsCreator.php';
require_once __DIR__ . '/../products/ProductsCreator.php';

trait ProductImportsCreator {
	use ImportsCreator, ProductsCreator;

	static private array $product_imports = [];
	static private iImport $default_import;
	static private iProduct $default_product;
	static private float $default_pimport_quantity = 0;
	static private float $default_purchase_price = 0;

	static function uniqueEmptyProductImportModel(iProduct $product = null, iImport $import = null): IProductImportModel {
		$product = $product ?? self::getUniqueProduct();
		$import = $import ?? self::getUniqueImport();
		return new ProductImportModel([
			'import_id' => $import->getImportId(),
			'product_id' => $product->getProductId(),
			'free_balance_quantity' => 0,
			'balance_quantity' => 0,
		]);
	}

	static function getUniqueProductDeficit(iProduct $product = null) : iProductImport {
		$old_quantity = self::$default_pimport_quantity;
		self::$default_pimport_quantity = self::$default_pimport_quantity / 4;

		$product = self::getProduct($product);
		self::createProductDeficit($product);

		self::$default_pimport_quantity = $old_quantity;

		$deficit = AbstractCreator::getProductImports()->getProductDeficit($product->getProductId());
		return $deficit;
	}

	static private function createProductDeficit (iProduct $product) : void {
		$imports = $imports_for_deficit = self::getUniqueImports(4);
		foreach ($imports as $import)
			self::getUniqueProductImport($import, $product);
		self::removeImportsForDeficit($imports);
	}

	static private function removeImportsForDeficit(array $imports) : void {
		foreach ($imports as $import)
			$import->delete();
	}

	static function uniqueProductImports(int $quantity, iProduct $product = null): array {
		$product = $product ?? self::getUniqueProduct();
		for($i = 0; $i < $quantity; $i++)
			$imports[] = self::getUniqueProductImport(null, $product);
		return $imports;
	}

	static function getUniqueProductImport(iImport $import = null, iProduct $product = null) : iProductImport {
		$import = self::getImport($import);
		$product = self::getProduct($product);
		self::linkTestProductToStoreByImport($product, $import);
		$product_import = self::createProductImport($import, $product);
		self::$product_imports[] = $product_import;
		return $product_import;
	}

	static function linkTestProductToStoreByImport(iProduct $product, iImport $import): void {
		TestPragmaFactory::getStoreApp()->getCategoriesToStores()->saveCategoryLinks($product->getCategoryId(), [$import->getStoreId()]);
	}

	static function getUniqueProductImportForStore(iProduct|null $product, iStore|null $store = null, float $quantity = null) : iProductImport {
		$product = $product ?? self::getUniqueProduct();
		$store = $store ?? self::getUniqueStore();
		$import = self::getUniqueImport($store);
		$product_import = self::createProductImport($import, $product, $quantity);
		self::$product_imports[] = $product_import;
		return $product_import;
	}

	static private function createProductImport (iImport $import, iProduct $product, float|null $quantity = null) : iProductImport {
        $quantity = $quantity ?? (self::$default_pimport_quantity ? self::$default_pimport_quantity : 1);
		return AbstractCreator::getProductImports()->createProductImport(
			$import->getImportId(),
			$product->getProductId(),
            $quantity,
			self::$default_purchase_price ? self::$default_purchase_price : 1
		);
	}

	static private function getImport ($import = null) : iImport {
		return $import ?? self::$default_import ?? self::getUniqueImport();
	}

	static private function getProduct($product = null) : iProduct {
		return $product ?? self::$default_product ?? self::getUniqueProduct();
	}

	static function clearAllFromProductImports () : void {
		self::clearProductImports();
//		self::clearProducts();
		self::clearCategories();
		self::clearImports();
		self::clearStores();
	}

	static function clearProductImports() : void {
		foreach (self::$product_imports as $product_import)
			$product_import->delete();
		self::$product_imports = [];
	}

	static function setDefaultImport(iImport $default_import): void {
		self::$default_import = $default_import;
	}

	static function setDefaultProduct(iProduct $default_product): void {
		self::$default_product = $default_product;
	}

	static function setDefaultPimportQuantity(int $default_pimport_quantity): void {
		self::$default_pimport_quantity = $default_pimport_quantity;
	}

	static function setDefaultPurchasePrice(float $default_purchase_price): void {
		self::$default_purchase_price = $default_purchase_price;
	}

//	public static function getProductImports(): array {
//		return self::$product_imports;
//	}

	public static function getDefaultImport(): iImport {
		return self::$default_import;
	}

	public static function getDefaultProduct(): iProduct {
		return self::$default_product;
	}

	public static function getDefaultPimportQuantity(): int {
		return self::$default_pimport_quantity;
	}

	public static function getDefaultPurchasePrice() {
		return self::$default_purchase_price;
	}
}