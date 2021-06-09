<?php


namespace PragmaStorage\Test;


use PragmaStorage\iProduct;

require_once __DIR__ . '/../../../../core_crm/modules/products/Products.php';

class TestProducts extends \PragmaStorage\Products {
	function uniqueProduct(): iProduct {
		$category = TestPragmaFactory::getTestCategories()->createCategory('sdfsdf');
		return $this->createProduct($category->getCategoryId(), uniqid('sdfsd'), uniqid('sdfsd'), 5.5);
	}
}