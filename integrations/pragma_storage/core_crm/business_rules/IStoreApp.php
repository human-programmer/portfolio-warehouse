<?php


namespace PragmaStorage;


interface IStoreApp {
	function getPragmaAccountId(): int;
	function getCategories(): iCategories;
	function getCategoriesToStores(): ICategoriesToStores;
	function getStores(): iStores;
	function getImports(): iImports;
	function getProductImports(): iProductImports;
	function getProducts(): iProducts;
	function getExports(): iExports;
	function getTravels(): ITravels;
	function getStorePriorities(): IStorePriorities;
	function getTravelLinks(): ITravelLinks;
}