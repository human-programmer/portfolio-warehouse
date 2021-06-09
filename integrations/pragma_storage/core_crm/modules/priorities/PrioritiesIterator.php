<?php


namespace PragmaStorage;


use PragmaStorage\Priorities\Sorter;

require_once __DIR__ . '/../../business_rules/priorities/IStorePriorityIterator.php';
require_once __DIR__ . '/Sorter.php';


class PrioritiesIterator implements IStorePriorityIterator {
	use Sorter;

	private array $priorities;
	private int $currentKey;

	function __construct(array $priorities) {
		$this->priorities = self::sortPriorities($priorities);
		$this->currentKey = 0;
	}

	function nextPriority(): IStorePriorityIterator|null {
		$this->next();
		return $this->priorities[$this->currentKey];
	}

	function current() {
		return $this->priorities[$this->currentKey] ?? null;
	}

	function next() {
		++$this->currentKey;
		$this->current = $this->priorities[$this->currentKey] ?? null;
	}

	function key() {
		return $this->currentKey;
	}

	function valid() {
		return !!$this->current();
	}

	function rewind() {
		$this->currentKey = 0;
	}

	function sortProductImports(array $productImports): array {
		$grouped = self::groupProductsImports($productImports);
		$grouped = self::sortByDate($grouped);
		return $this->sortByPriority($grouped);
	}

	function sortStores(array $stores): array {
		$grouped = self::groupStores($stores);
		return $this->sortStoresByPriority($grouped) ?? self::sortByStoreId($stores);
	}

	function sortExports(array $exports, int $store_id): array {
		$grouped = self::groupByPriorityExports($exports, $store_id);
		$grouped = self::sortGroupedExportsByDateCreate($grouped);
		return $this->sortGroupedExportsByPriority($grouped) ?? array_merge(...array_values($grouped));
	}
}