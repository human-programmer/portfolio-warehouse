<?php


namespace PragmaStorage;


require_once __DIR__ . '/../basics/iBasics.php';


interface iStores {
	function save(iStore $store): void;
	function getStores(int $archive_status = UNARCHIVED_STATUS): array;
	function createStore(string $title, string $address): iStore;
	function getStore(int $store_id): iStore;
	function deleteStore(iStore $store): bool;
	function archiveStore(iStore $store): bool;

	function getAvailableStores(int $product_id): array;
	function allowedForStore(int $store_id, int $product_id): bool;
}