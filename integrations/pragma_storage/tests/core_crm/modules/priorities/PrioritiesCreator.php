<?php


namespace PragmaStorage\Exports\Tests;


use PragmaStorage\iExport;
use PragmaStorage\iStore;
use PragmaStorage\IStoreExportPriority;
use PragmaStorage\StorePriorities;
use PragmaStorage\StorePriorityModel;
use PragmaStorage\Test\ExportsCreator;
use PragmaStorage\Test\TestPragmaFactory;

require_once __DIR__ . '/../../../../core_crm/modules/priorities/StorePriorityModel.php';
require_once __DIR__ . '/../../../../core_crm/modules/priorities/StorePriorities.php';

trait PrioritiesCreator {
	use ExportsCreator;

	static function uniquePriorityModels(array $stores, iExport $export = null): array {
		$stores = array_values($stores);
		$export = $export ?? self::getUniqueExport();
		foreach ($stores as $index => $store)
			$models[] = new StorePriorityModel(['store_id' => $store->getStoreId(), 'export_id' => $export->getExportId(), 'sort' => $index]);
		$fabric = new StorePriorities(TestPragmaFactory::getStoreApp());
		$fabric->savePriorities($export->getExportId(), $models);
		return $fabric->getPriorities([$export->getExportId()])[$export->getExportId()];
	}

	static function uniquePriorityModel(int $sort, iStore $store = null, iExport $export = null): IStoreExportPriority {
		$store = $store ?? self::getUniqueStore();
		$export = $export ?? self::getUniqueExport();
		return new StorePriorityModel(['store_id' => $store->getStoreId(), 'export_id' => $export->getExportId(), 'sort' => $sort]);
	}

	static function clearPriorities(): void {
		self::clearExports();
		self::clearStores();
	}
}