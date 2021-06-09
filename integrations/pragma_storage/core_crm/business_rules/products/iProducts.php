<?php


namespace PragmaStorage;


interface iProducts {
	function save (iProduct $product) : void;
	function deleteProduct (int $product_id) : bool;
    function createProduct (int $category_id, string $article, string $title, float $selling_price, array $model = []) : iProduct;
    function getProducts (int $category_id = null) : array;
    function findProduct (string $article);
    function getProduct (int $product_id) : iProduct;
    function setDeleted (iProduct $product) : bool;
    function preloadProducts(array $product_id): void;
    function addHandler(IProductCreationHandler $handler): void;
}