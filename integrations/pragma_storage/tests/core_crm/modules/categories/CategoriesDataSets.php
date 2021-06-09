<?php


namespace PragmaStorage\Test;


use PragmaStorage\iCategories;

trait CategoriesDataSets {
	function getCategoriesTitles () : array {
		return [
			['test1'],
			[' test2'],
			['test3 '],
			[''],
			['  '],
			['test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5test5'],
		];
	}

	static protected function getCategories () : iCategories {
		return TestPragmaFactory::getTestCategories();
	}
}