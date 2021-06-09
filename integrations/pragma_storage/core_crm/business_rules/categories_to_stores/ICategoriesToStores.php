<?php


namespace PragmaStorage;


interface ICategoriesToStores {
	function saveCategoryLinks(int $category_id, array $stores_id): void;
	function saveLinks(array $links): void;
	function getCategoriesIdInStore(int $store_id): array;
	function getProductsIdInStore(int $store_id): array;
	function getStoresIdForProduct(int $product_id): array;
	function getStoresForCategory(int $category_id): array;
	function getProductsIdOfCategory(int $category_id): array;
	function getCategoryIdOfProduct(int $product_id): int;
	function getProductCreationHandler(): IProductCreationHandler;
}