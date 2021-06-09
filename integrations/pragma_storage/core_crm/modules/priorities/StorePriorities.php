<?php


namespace PragmaStorage;


use PragmaStorage\IStoreApp;
use PragmaStorage\IStoreExportPriority;

require_once __DIR__ . '/../../business_rules/priorities/IStorePriorities.php';
require_once __DIR__ . '/StorePrioritiesSchema.php';
require_once __DIR__ . '/StorePriorityModel.php';

class StorePriorities implements IStorePriorities {

	function __construct(private IStoreApp $app) {}

	function savePriorities(int $export_id, array $priorities): void {
		StorePrioritiesSchema::clearPriorities([$export_id]);
		StorePrioritiesSchema::savePriorities($priorities);
	}

	function getPriorities(array $export_id): array {
		$current = self::getCurrentPriorities($export_id);
		foreach ($export_id as $id)
			$res[$id] = $this->prioritiesForExport($id, $current[$id] ?? null);
		return $res ?? [];
	}

	private static function getCurrentPriorities(array $export_id): array {
		$models = StorePrioritiesSchema::getPriorityModels($export_id);
		foreach ($models as $model)
			$res[$model['export_id']][] = self::createPriorityInst($model);
		return $res ?? [];
	}

	private function prioritiesForExport(int $export_id, array|null $current_priorities): array {
		if(!$current_priorities) return $this->defaultPriorities($export_id);
		return $this->diffWithAvailable($export_id, $current_priorities) ?? $this->defaultPriorities($export_id);
	}

	private function diffWithAvailable(int $export_id, array $exports_priorities): array|null {
		$available_stores_id = $this->getStoresIdForExport($export_id);
		foreach ($exports_priorities as $exports_priority)
			if(array_search($exports_priority->getStoreId(), $available_stores_id) !== false)
				$result[] = $exports_priority;
		return $result ?? null;
	}

	private function defaultPriorities(int $export_id): array {
		$stores_id = $this->getSortedStoresIdForExport($export_id);
		foreach ($stores_id as $index => $store_id)
			$priorities[] = self::createPriorityInst([
				'store_id' => $store_id,
				'export_id' => $export_id,
				'sort' => $index
			]);
		return $priorities ?? [];
	}

	private function getSortedStoresIdForExport(int $export_id): array {
		$stores_id = $this->getStoresIdForExport($export_id);
		sort($stores_id);
		return $stores_id;
	}

	private function getStoresIdForExport(int $export_id): array {
		$stores_id = $this->getAvailableStoresId($export_id);
		if(count($stores_id)) return $stores_id;
		return $this->getAllStoresId();
	}

	private function getAvailableStoresId(int $export_id): array {
		$product_id = $this->app->getExports()->getExport($export_id)->getProductId();
		return $this->app->getCategoriesToStores()->getStoresIdForProduct($product_id);
	}

	private function getAllStoresId(): array {
		$stores = $this->app->getStores();
		foreach ($stores as $store)
			$result[] = $store->getStoreId();
		return $result ?? [];
	}

	static function createPriorityInst(array $model): IStoreExportPriority {
		return new StorePriorityModel($model);
	}

	function createIterator(IExportModel $export): IStorePriorityIterator {
		return new PrioritiesIterator($export->getAvailablePriorities());
	}

	function createIteratorForProductImport(iProductImport $productImport): IStorePriorityIterator {
		$exports = $productImport->getExportItems();
		$store_id = $productImport->findStoreId() ?? 0;
		$priorities = [];
		foreach ($exports as $export)
			$priorities = array_merge($priorities, $export->getPriorities());
		foreach ($priorities as $priority)
			if($priority->getStoreId() === $store_id)
				$result[] = $priority;
		return new PrioritiesIterator($result ?? []);
	}
}