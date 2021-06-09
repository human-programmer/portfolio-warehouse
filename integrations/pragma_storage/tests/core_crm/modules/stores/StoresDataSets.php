<?php


namespace PragmaStorage\Test;


use PragmaStorage\iImports;
use PragmaStorage\iProductImports;
use PragmaStorage\iProducts;
use PragmaStorage\iStores;

trait StoresDataSets {
	function setsForCreate () : array {
		return [
			[
				time() . 'test title1',
				time() . 'test address1',
			],
			[
				'  ',
				time() . 'test address2',
			],
			[
				time() . 'test title3',
				'  ',
			],
			[
				'',
				time() . 'test address21',
			],
			[
				time() . 'test title31',
				'',
			],
			[
				time() . ' test title4  ',
				time() . '  test address4  ',
			],
			[
				time() . ' test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5   test title5    test title5    test title5    test title5    test title5    test title5    test title5    test title5    test title5  ',
				time() . '  test address5  ',
			],
			[
				time() . ' test title6  ',
				time() . '  test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6    test address6  ',
			],
		];
	}

	static protected function getStoresWithOwnImports() : array {

	}

	static protected function getStores () : iStores {
		return TestPragmaFactory::getTestStores();
	}

	static protected function getImports() : iImports {
		return TestPragmaFactory::getTestImports();
	}

	static protected function getProducts() : iProducts {
		return TestPragmaFactory::getTestProducts();
	}

	static protected function getProductImports() : iProductImports {
		return TestPragmaFactory::getTestProductImports();
	}

	static protected function formattingAsVarchar (string $string) : string {
		return substr(trim($string), 0, 256);
	}
}