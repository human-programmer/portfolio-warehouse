<?php


namespace PragmaStorage\Test;


use PragmaCRM\EntitiesValueStorage;
use PragmaCRM\Factory;
use PragmaCRM\iValueStorage;
use PragmaCRM\Test\iTestEntities;
use PragmaCRM\Test\TestFactory;
use PragmaStorage\iCategories;
use PragmaStorage\iExports;
use PragmaStorage\iImports;
use PragmaStorage\iProductImports;
use PragmaStorage\iProducts;
use PragmaStorage\iStorage;
use PragmaStorage\IStoreApp;
use PragmaStorage\iStores;
use PragmaStorage\PragmaFactory;
use PragmaStorage\Storage;
use PragmaStorage\StoreApp;
use PragmaStorage\Test\Storage\TestStorage;

require_once __DIR__ . '/../../../../lib/services/tests/TestFactory.php';
require_once __DIR__ . '/../../../pragmacrm/test/TestFactory.php';
require_once __DIR__ . '/../../core_crm/PragmaFactory.php';
require_once __DIR__ . '/modules/categories/TestCategories.php';
require_once __DIR__ . '/modules/stores/TestStores.php';
require_once __DIR__ . '/modules/products/TestProducts.php';
require_once __DIR__ . '/modules/imports/TestImports.php';
require_once __DIR__ . '/modules/product_imports/TestProductImports.php';
require_once __DIR__ . '/modules/exports/TestExports.php';
require_once __DIR__ . '/modules/entities/TestEntities.php';
require_once __DIR__ . '/modules/storage/TestStorage.php';

require_once __DIR__ . '/modules/AbstractCreator.php';
require_once __DIR__ . '/modules/users/UsersCreator.php';
require_once __DIR__ . '/modules/stores/StoresCreator.php';
require_once __DIR__ . '/modules/products/ProductsCreator.php';
require_once __DIR__ . '/modules/exports/ExportsCreator.php';
require_once __DIR__ . '/modules/details/DetailsCreator.php';
require_once __DIR__ . '/modules/travels/TravelCreator.php';
require_once __DIR__ . '/modules/priorities/PrioritiesCreator.php';
require_once __DIR__ . '/modules/travel_export_link/TravelLinksCreator.php';

class TestPragmaFactory extends PragmaFactory {
	static private bool $is_init = false;

	static function ifInitTest() : void {
		if(!self::isTestInit())
			self::init_test();
	}

	static function isTestInit() : bool {
		return self::$is_init;
	}

	static function resetStoreApp(): void {
		self::$storeApp = new Storage (self::getPragmaAccountId());
	}

	static function init_test(): void {
		\Services\Tests\TestFactory::uniqueNode();
		self::$node = \Services\Tests\TestFactory::uniqueNode();
		self::$account = self::$node->getAccount();
		self::$log_writer = new \LogJSON('unit_tests_storage', 'storage');
		self::$is_init = true;
	}

	static function createStoreAppWithNewAccount(): IStoreApp {
		$uniqueNode = \Services\Tests\TestFactory::uniqueNode();
		return new StoreApp($uniqueNode->getAccount()->getPragmaAccountId());
	}

	static function ifAmocrmInitTest() : void {
		if(!self::isTestInit())
			self::amocrm_init_test();
	}
	static function amocrm_init_test(): void {
		Factory::init_test();
		self::$is_init;
	}


	static function getTestCategories () : iCategories {
		return new TestCategories(self::getStoreApp());
	}

	static function resetStores(): void {
		self::$storeApp = new Storage (self::getPragmaAccountId());
	}

	static function getTestStores () : iStores {
		return new TestStores(self::getStoreApp());
	}

	static function getTestProducts() : TestProducts {
		$test = new TestProducts(self::getStoreApp());
		$test->addHandler(self::getStoreApp()->getCategoriesToStores()->getProductCreationHandler());
		return $test;
	}

	static function getTestImports() : iImports {
		return new TestImports(self::getPragmaAccountId());
	}

	static function getTestProductImports () : iProductImports {
		return new TestProductImports(self::getStoreApp());
	}

	static function getTestExports() : iExports {
		return new TestExports(self::getStoreApp());
	}

	static function getTestEntities() : iTestEntities {
		return TestFactory::getTestEntities();
	}

	static function getTestStorage () : iStorage {
		return new TestStorage(self::getPragmaAccountId());
	}

	static function getCRMEntitiesStorage() : EntitiesValueStorage {
		return self::getCrmStorage()->getEntitiesStorage();
	}

	static function getCRMValuesStorage () : iValueStorage {
		return self::getCrmStorage();
	}
}