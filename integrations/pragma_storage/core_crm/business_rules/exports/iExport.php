<?php


namespace PragmaStorage;

require_once __DIR__ . '/IExportModel.php';

interface iExport extends iBasis, IExportModel {
	function setDeleted(): void;
	function getDetails(): array;
	function getProduct(): iProduct;
	function getDetailsQuantity(): float;
	function saveDeletedEntity () : void;
	function getPrioritySort(int|null $store_id): int|null;
	function updateDetails(iProductImport $productImport = null): bool;
	function isExported(): bool;
	function getProductsImports(): array;
	function getTotalPurchasePrice(): float;
}