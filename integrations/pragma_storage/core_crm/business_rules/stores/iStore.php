<?php


namespace PragmaStorage;


require_once __DIR__ . '/../basics/iBasis.php';


interface iStore extends iBasis {
	function getStoreId(): int;
	function getTitle(): string;
	function getAddress(): string;
	function getOwnImports(): array;
	function getOwnProductImports(): array;
	function getOwnCategoriesId(): array;
	function getOwnProductsId(): array;
	function setDeleted(): void;
	function addCategory(iCategory $category): void;
}