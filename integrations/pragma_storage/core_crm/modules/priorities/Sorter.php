<?php


namespace PragmaStorage\Priorities;


trait Sorter {
	static function sortPriorities(array $priorities): array {
		$priorities = $priorities ?? [];
		$res = [];
		foreach ($priorities as $priority)
			$res[$priority->getSort()][] = $priority;
		ksort($res);
		return self::uniquePriorities(array_merge(...array_values($res)));
	}

	private static function uniquePriorities(array $priorities): array {
		foreach ($priorities as $priority)
			$result[$priority->getStoreId() . '.' . $priority->getExportId()] = $priority;
		return array_values($result ?? []);
	}

	private static function groupProductsImports(array $productsImports): array {
		$grouped = [];
		foreach ($productsImports as $productImport)
			$grouped[$productImport->findStoreId() ?? 0][] = $productImport;
		return $grouped;
	}

	private static function sortByDate(array $groups): array {
		foreach ($groups as $index => $productsImports)
			$result[$index] = self::sortByDateCreate($productsImports);
		return $result ?? [];
	}

	private static function sortByDateCreate(array $productImports): array {
		$result = [];
		foreach ($productImports as $productImport)
			$result[$productImport->getDateCreate()][] = $productImport;
		ksort($result);
		return array_merge(...array_values($result));
	}

	private function sortByPriority(array $grouped): array {
		foreach ($this->priorities as $priority)
			$result[] = $grouped[$priority->getStoreId()] ?? [];
		if(isset($result)) return array_merge(...$result);
		$productsImports = array_merge(...array_values($grouped));
		return self::sortByDateCreate($productsImports);
	}

	private static function groupStores(array $stores): array {
		$grouped = [];
		foreach ($stores as $store)
			$grouped[$store->getStoreId()] = $store;
		return $grouped;
	}

	private function sortStoresByPriority(array $grouped): array|null {
		foreach ($this->priorities as $priority)
			if(isset($grouped[$priority->getStoreId()]))
				$result[] = $grouped[$priority->getStoreId()];
		return $result ?? null;
	}

	private static function sortByStoreId(array $stores): array {
		$result = [];
		foreach ($stores as $store)
			$result[$store->getStoreId()] = $store;
		ksort($result);
		return array_values($result);
	}

	private static function groupByPriorityExports(array $exports, int $store_id): array {
		foreach ($exports as $export)
			$result[$export->getPrioritySort($store_id) ?? 0][] = $export;
		return $result ?? [];
	}

	private static function sortGroupedExportsByDateCreate(array $grouped): array {
		foreach ($grouped as $index => $exports)
			$result[$index] = self::sortExportsByDateCreate($exports);
		return $result ?? [];
	}

	private static function sortExportsByDateCreate(array $exports): array {
		$res = [];
		foreach ($exports as $export)
			$res[$export->getExportId()] = $export;
		ksort($res);
		return array_values($res);
	}

	private function sortGroupedExportsByPriority(array $grouped): array|null {
		foreach ($this->priorities as $priority)
			$result[] = $grouped[$priority->getSort()] ?? [];
		return isset($result) ? self::uniqueExports(array_merge(...$result)) : null;
	}

	private static function uniqueExports(array $exports): array {
		foreach ($exports as $export)
			$result[$export->getExportId()] = $export;
		return array_values($result ?? []);
	}
}