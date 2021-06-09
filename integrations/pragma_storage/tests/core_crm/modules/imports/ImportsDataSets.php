<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImports;
use PragmaStorage\iStore;
use PragmaStorage\iStores;

trait ImportsDataSets {
	static private array $stores = [];

	static function dataSetsForCreate () : array {
		if(!TestPragmaFactory::isTestInit())
			TestPragmaFactory::init_test();

		$store_1 = self::getUniqueStore();
		$store_2 = self::getUniqueStore();
		return [
			[
				$store_1,
				[
					'provider' => 'test_provider',
					'date' => null,
				]
			],
			[
				$store_1,
				[
					'provider' => 'test_provider',
					'date' => self::getFormattedTimeStamp(time() - 86400),
				]
			],
			[
				$store_1,
				[
					'provider' => 'test_provider',
				]
			],
			[
				$store_2,
				[
					'provider' => 'test_provider',
					'date' => null,
				]
			],
			[
				$store_2,
				[
					'provider' => 'test_provider',
					'date' => self::getFormattedTimeStamp(time() - 86400),
				]
			],
			[
				$store_2,
				[
					'provider' => 'test_provider',
				]
			],
		];
	}

	static protected function dataSetsForUpdate () : array {

		return [
			[
				'store' => self::getUniqueStore(),
				'import_date' => self::getFormattedTimeStamp(time()),
				'provider' => self::getUniqueString()
			],
			[
				'store' => self::getUniqueStore(),
				'provider' => self::getUniqueString()
			],
			[
				'store' => self::getUniqueStore(),
				'provider' => '   test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test   '
			],
			[
				'provider' => '   test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test     test   '
			],
			[
				'provider' => '   '
			],
			[
				'provider' => ''
			],
			[
				'' => self::getUniqueStore(),
				'import_date' => self::getFormattedTimeStamp(time()),
			],
			[
				'store' => self::getUniqueStore()
			],
		];
	}

	static protected function getModelFromSet(array $set) : array {
		if(isset($set['store']))
			$result['store_id'] = $set['store']->getStoreId();

		if(isset($set['import_date']))
			$result['import_date'] = $set['import_date'];

		if(isset($set['provider']))
			$result['provider'] = $set['provider'];

		return $result ?? [];
	}

	static protected function getUniqueStore () : iStore {
		$store = self::getStores()->createStore(self::getUniqueString(), self::getUniqueString());
		self::$stores[] = $store;
		return $store;
	}

	static protected function getStores() : iStores {
		return TestPragmaFactory::getStores();
	}

	static protected function getImports () : iImports {
		return TestPragmaFactory::getTestImports();
	}

	static protected function getUniqueString() : string {
		return uniqid('string');
	}

	static protected function getFormattedTimeStamp(int $time): string {
		return date('Y-m-d H:i:s', $time);
	}

	static protected function clearStores() : void {
//		foreach (self::$stores as $store)
//			$store->delete();
	}

	static function formattingAsVarchar(string $string) : string {
		return trim(substr(trim($string), 0, 256));
	}
}