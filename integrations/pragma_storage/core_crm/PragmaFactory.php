<?php


namespace PragmaStorage;


use PragmaCRM\iIntegrationActionsHandler;
use PragmaCRM\iIntegrationFactory;
use Services\General\iRemovalInspector;

require_once __DIR__ . '/../../pragmacrm/core_crm/PragmaFactory.php';
require_once __DIR__ . '/modules/db/PragmaStoreDB.php';
require_once __DIR__ . '/../../pragmacrm/core_crm/iIntegrationFactory.php';
require_once __DIR__ . '/modules/files/Files.php';
require_once __DIR__ . '/modules/Storage.php';
require_once __DIR__ . '/modules/discount/Discount.php';
require_once __DIR__ . '/modules/settings/Settings.php';
require_once __DIR__ . '/modules/StorageActionsHandler.php';
require_once __DIR__ . '/../CONSTANTS.php';
require_once __DIR__ . '/Installer.php';
require_once __DIR__ . '/RemovalInspector.php';


class PragmaFactory extends \PragmaCRM\PragmaFactory implements iIntegrationFactory {
	private static StorageActionsHandler $storage_actions_handler;
	protected static IStoreApp $storeApp;
    private static iSettings $settings;
    private static iDiscount $discount;


    static function getPragmaStoreEventHandler(): iIntegrationActionsHandler {
		if (isset(self::$storage_actions_handler))
			return self::$storage_actions_handler;

		self::$storage_actions_handler = new StorageActionsHandler(\PragmaCRM\PragmaFactory::getPragmaAccountId());

		return self::$storage_actions_handler;
	}

	static function getStoreApp() {
		if (isset(self::$storeApp))
			return self::$storeApp;

		self::$storeApp = new Storage (self::getPragmaAccountId());

		return self::$storeApp;
	}

	static function getStatusToStatus(): iStatusToStatus {
		return self::getStoreApp()->getStatusToStatus();
	}

	static function getStores(): iStores {
		return self::getStoreApp()->getStores();
	}

	static function getCategories(): iCategories {
		return self::getStoreApp()->getCategories();
	}

	static function getImports(): iImports {
		return self::getStoreApp()->getImports();
	}

	static function getProductImports(): iProductImports {
		return self::getStoreApp()->getProductImports();
	}

	static function getExports(): iExports {
		return self::getStoreApp()->getExports();
	}

	static function getProducts(): iProducts {
		return self::getStoreApp()->getProducts();
	}

	static function getExportDetails(): iExportDetails {
		return self::getStoreApp()->getExportDetails();
	}

	static function getStatuses(): iStatuses {
		return self::getStoreApp()->getStatuses();
	}

//	static function getStoreEntities(): iCrmEntities {
//		return self::getCrmStorage();
//	}

	static function getEntities(): iEntities {
		return self::getStoreApp()->getEntities();
	}

    static function getInstaller(): Installer
    {
        return new Installer();
    }

    public static function getSettings(): iSettings
    {
        if (isset(self::$settings))
            return self::$settings;
        self::$settings = new Settings (self::getPragmaAccountId());
        return self::$settings;
    }


	static function getFiles(): iFiles
    {
		return new Files(self::getPragmaAccountId());
	}

	static function getDiscount(): iDiscount
    {
        if (isset(self::$discount))
            return self::$discount;
        self::$discount = new Discount( self::getPragmaAccountId());
        return self::$discount;

    }

	static function getUnits(): iProducts {
		return new Products(self::getStoreApp());
	}

	static function getRemovalInspector(): iRemovalInspector {
    	return new RemovalInspector();
	}
}