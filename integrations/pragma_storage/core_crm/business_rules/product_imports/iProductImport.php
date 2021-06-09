<?php


namespace PragmaStorage;

require_once __DIR__ . '/IProductImportModel.php';

interface iProductImport extends iBasis, IProductImportModel {
	function getProduct(): iProduct;
	function getExportDetails(): array;
	function getOwnedExports(): array;
	function getExportQuantity(): float;
	function getExportItems(): array;

	function updateBalance(): void;
	function isExported(): bool;
	function findImport(): iImport|null;
	function save(): bool;
	function findStoreId(): null|int;
}