<?php


namespace PragmaStorage;


interface IProductImportModel {
	function setQuantity(float $new_quantity): void;
	function getProductImportId(): int;
	function getProductId(): int;
	function getImportId(): int|null;
	function getImportQuantity(): float;
	function getPurchasePrice(): float;
	function setPurchasePrice(float $price): void;
	function getSource(): int;
	function isDeficit(): bool;
	function getFreeBalanceQuantity(): float;
	function getBalanceQuantity(): float;
	function getImportDate(): int;
	function getDateCreate(): int;
	function toArray(): array;
}