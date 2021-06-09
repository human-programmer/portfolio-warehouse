<?php


namespace PragmaStorage;


interface IStorePriorityIterator extends \Iterator {
	function nextPriority(): IStorePriorityIterator|null;
	function sortProductImports(array $productImports): array;
	function sortStores(array $stores): array;
	function sortExports(array $exports, int $store_id): array;
}