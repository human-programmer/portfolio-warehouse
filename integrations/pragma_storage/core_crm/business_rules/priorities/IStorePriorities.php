<?php


namespace PragmaStorage;


interface IStorePriorities {
	function savePriorities(int $export_id, array $priorities): void;
	function getPriorities(array $export_id): array;
	function createIterator(IExportModel $export): IStorePriorityIterator;
	function createIteratorForProductImport(iProductImport $productImport): IStorePriorityIterator;
}