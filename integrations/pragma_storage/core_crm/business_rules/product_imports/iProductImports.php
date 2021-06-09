<?php


namespace PragmaStorage;


interface iProductImports{
    function getProductDeficit (int $product_id, int $store_id): iProductImport;
    function getProductImport (int $product_import_id) : iProductImport;
    function getByIdList(array $product_import_id): array;
    function findFreeProductImport (int $product_id, int|null $store_id = null): iProductImport|null;
    function getProductImports (int $product_id, int|array $store_id = null) : array;
    function getImportProductImports (int $import_id) : array;
    function getAllImportProductImports (array $import_ids) : array;

    function preloadProductImports(array $product_id): void;
    function createModel(array $model): IProductImportModel;

	function createProductImport (int $import_id, int $product_id, float $quantity, float $purchase_price) : iProductImport;
	function create(IProductImportModel $model): iProductImport;

    function createTravelsImport(ITravelModel $travel, int $product_id): iProductImport;

    function deleteProductImport(iProductImport $productImport): void;
	function save(IProductImportModel $productImportModel): void;
}