<?php


namespace PragmaStorage\Test;


use PragmaStorage\ExportStruct;
use PragmaStorage\iEntity;
use PragmaStorage\iExport;
use PragmaStorage\IExportModel;
use PragmaStorage\iProduct;
use const PragmaStorage\EXPORT_STATUS_RESERVED;
use const PragmaStorage\STORE_SOURCE;

require_once __DIR__ . '/../product_imports/ProductImportsCreator.php';
require_once __DIR__ . '/../entities/EntitiesCreator.php';

trait ExportsCreator {
	use ProductImportsCreator, EntitiesCreator;

	static private iEntity $default_entity;
	static private float $default_selling_price;
	static private float $default_export_quantity;
	static private array $exports = [];

	private function uniqueExportModel(array $model = []): IExportModel {
        $model['pragma_entity_id'] = isset($model['pragma_entity_id']) ? $model['pragma_entity_id'] : null;
        $model['product_id'] = isset($model['product_id']) ? $model['product_id'] : self::fetchProductId($model);
        $model['quantity'] = isset($model['quantity']) ? $model['quantity'] : 0;
        $model['selling_price'] = isset($model['selling_price']) ? $model['selling_price'] : 0;
        $model['status_id'] = isset($model['status_id']) ? $model['status_id'] : EXPORT_STATUS_RESERVED;
        $model['id'] = isset($model['id']) ? $model['id'] : 0;
        $model['client_type'] = isset($model['client_type']) ? STORE_SOURCE : $model['id'];
        return new ExportStruct($model);
    }

//    private static function fetchPragmaEntityId(array $model): int|null {
//	    return isset($model['pragma_entity_id']) ? $model['pragma_entity_id'] : self::getUniqueEntity()->getPragmaEntityId();
//    }

    private static function fetchProductId(array $model): int|null {
	    return isset($model['product_id']) ? $model['product_id'] : self::getUniqueProduct()->getProductId();
    }

	static function getUniqueExport (iEntity $entity = null) : iExport {
		$entity = self::getTestEntity($entity);
		return self::createExport($entity);
	}

	static private function getUniqueExportForProduct(iProduct $product, int $quantity = 10): iExport {
		$export = AbstractCreator::getExports()->createExport(self::getDefaultEntity(), $product, $quantity, self::getDefaultSellingPrice());
		self::$exports[] = $export;
		return $export;
	}

	static private function createExport (iEntity $entity) : iExport {
		$product = self::$default_product ?? self::getUniqueProduct();
		$quantity = self::$default_export_quantity ?? 100;
		$price = self::$default_selling_price ?? 1000;
		$export = AbstractCreator::getExports()->createExport($entity, $product, $quantity, $price);
		self::$exports[] = $export;
		return $export;
	}

	static private function clearExports(): void {
		foreach (self::$exports as $export)
			$export->delete();
	}

	static private function getTestEntity ($entity = null) : iEntity {
		return $entity ?? self::$default_entity ?? self::getUniqueEntity();
	}

	public static function getDefaultEntity(): iEntity {
		return self::$default_entity ?? self::getUniqueEntity();
	}

	public static function setDefaultEntity(iEntity $default_entity): void {
		self::$default_entity = $default_entity;
	}

	public static function getDefaultSellingPrice(): float {
		return self::$default_selling_price ?? 0;
	}

	public static function setDefaultSellingPrice(float $default_selling_price): void {
		self::$default_selling_price = $default_selling_price;
	}

	public static function getDefaultExportQuantity(): float {
		return self::$default_export_quantity;
	}

	public static function setDefaultExportQuantity(float $default_export_quantity): void {
		self::$default_export_quantity = $default_export_quantity;
	}
}