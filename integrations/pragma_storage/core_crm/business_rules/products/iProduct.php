<?php


namespace PragmaStorage;


interface iProduct extends iBasis
{
    function getProductId(): int;
    function getCategoryId();
    function getTitle() : string;
	function getUnit() : string;
    function getArticle() : string;
    function getSellingPrice(): float;
    function getExports () : array;
    function getOwnedProductImports() : array;
    function getFreeQuantity(int $store_id): float;
    function getLinkedStores(): array;
}