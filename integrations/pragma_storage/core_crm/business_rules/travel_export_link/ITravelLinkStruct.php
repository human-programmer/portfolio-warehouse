<?php


namespace PragmaStorage;


interface ITravelLinkStruct {
	function getTravelId(): int;
	function getProductId(): int;

	function getStartExportId(): int|null;
	function setStartExportId(int $export_id): void ;

	function getReceiveProductImportId(): int|null;
	function setReceiveProductImportId(int $product_import_id): void;

	function getQuantity(): float;
	function setQuantity(float $quantity): void;

	function toArray(): array;
}