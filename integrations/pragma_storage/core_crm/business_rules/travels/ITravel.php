<?php


namespace PragmaStorage;

require_once __DIR__ . '/ITravelModel.php';

interface ITravel extends ITravelModel {
	function getCreationDate(): int;
	//$products['product_id', 'quantity']
	function addProducts(array $products): void;
	function addProduct(int $product_id, float $quantity): void;
    function getLinks(): array;
	function findTravelLink(int $product_id): ITravelLink|null;
	function getTravelStatus(): int;
	function setDeliveredStatus(): void;
	function toArray(): array;
}