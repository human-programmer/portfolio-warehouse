<?php


namespace PragmaStorage;


use PragmaStorage\Test\AbstractCreator;
use PragmaStorage\Test\ExportsCreator;
use PragmaStorage\Test\ProductImportsCreator;

trait DetailsCreator {
	use ExportsCreator, ProductImportsCreator;
	static private array $details = [];


	static function uniqueDetail(iExport $export = null, iProductImport $import = null): iExportDetail {
		$export = $export ?? self::getUniqueExport();
		$import = $import ?? self::getUniqueProductImport(null, $export->getProduct());
		$detail = AbstractCreator::getTestDetails()->getExportDetail($export, $import);
		$detail->setQuantity($export->getQuantity());
		self::$details[] = $detail;
		return $detail;
	}

	static function clearDetails(): void {
		self::clearExports();
		self::clearProductImports();
		self::clearProducts();
		self::$details = [];
	}
}