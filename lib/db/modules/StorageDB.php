<?php


namespace Generals;


use Configs\Configs;

trait StorageDB {

    static function getStorageCategoriesSchema () : string {
        return '`' . self::getStorageDB() . '`.`categories`';
    }

    static function getStorageImportsSchema () : string {
        return '`' . self::getStorageDB() . '`.`imports`';
    }

    static function getStorageProductsSchema () : string {
        return '`' . self::getStorageDB() . '`.`products`';
    }

    static function getStorageProductExportsSchema () : string {
        return '`' . self::getStorageDB() . '`.`product_exports`';
    }

    static function getStorageProductExportsDetailsSchema () : string {
        return '`' . self::getStorageDB() . '`.`product_export_details`';
    }

    static function getStorageProductImportsSchema () : string {
        return '`' . self::getStorageDB() . '`.`product_imports`';
    }

    static function getStorageStatusesSchema () : string {
        return '`' . self::getStorageDB() . '`.`statuses`';
    }

    static function getStorageStatusToStatusSchema () : string {
        return '`' . self::getStorageDB() . '`.`status_to_status`';
    }

    static function getStorageStoresSchema () : string {
        return '`' . self::getStorageDB() . '`.`stores`';
    }

    static function getStorageDeletedEntitiesToExportsSchema () : string {
        return '`' . self::getStorageDB() . '`.`deleted_entities_to_exports`';
    }

	static function getStorageTravelsSchema(): string {
		return '`' . self::getStorageDB() . '`.`travels`';
	}

	static function getStorageExportToTravelSchema(): string {
		return '`' . self::getStorageDB() . '`.`export_to_travel`';
	}

	static function getStorageExportPrioritiesSchema(): string {
		return '`' . self::getStorageDB() . '`.`store_priorities`';
	}

	static function getStorageCategoriesToStoresSchema(): string {
		return '`' . self::getStorageDB() . '`.`categories_to_stores`';
	}

    static function getStorageDB () : string {
        return Configs::getDbNames()->getStorage();
    }
}