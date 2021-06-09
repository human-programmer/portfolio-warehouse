<?php


namespace PragmaStorage;


interface IExportModel {
	function getExportId(): int;
	function getEntityId(): int|null;
	function getProductId(): int;
	function getQuantity(): float;
	function setQuantity(float $quantity);
	function getSellingPrice(): float;
	function setSellingPrice(float $price): void;
	function getStatusId(): int;
	function setStatus(int $status_id): bool;
    function getAvailablePriorities(): array;
	function getPriorities(): array;
	function setPriorities(array $priorities): void;
	function getHighestPriority(): IStoreExportPriority;
	function getClientType(): int;
	function toArray(): array;
}