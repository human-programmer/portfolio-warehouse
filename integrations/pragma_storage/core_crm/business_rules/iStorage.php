<?php


namespace PragmaStorage;


interface iStorage
{
    //Settings
    /*
     *  [
     * [`export_status_id` - статус товара
        `entity_status_id` - статус сделки
        `pipeline_id`] - статус воронки
        ]
     */
    function setStatusLinks (array $links): bool;

    function getStatusLinks (): array;

    function getExportStatuses () : array;

    //Categories
    function createCategory (string $title) : iCategory;

    function getCategory (int $category_id) : iCategory;

    function deleteCategory (int $category_id) : bool;

    //Stores
    function createStore (string $title, string $address) : iStore;

    function getStore (int $store_id) : iStore;

    function deleteStore (int $store_id) : bool;

    //Products
    function deleteProduct (int $product_id) : bool;

    /*
     * $model['unit'] - единица измерения
     */
    function createProduct (int $category_id, string $article, string $title, float $selling_price, array $model = []) : iProduct;

    function getProduct (int $product_id) : iProduct;

    //imports
    function createImport(int $store_id, array $model): iImport;

    function getImport (int $import_id) : iImport;

    function deleteImport (int $import_id) : bool;

    //ProductImports
    function createProductImport (int $import_id, int $product_id, float $quantity, float $purchase_price) : iProductImport;

    function getProductImport (int $product_import_id) : iProductImport;

    function deleteProductImport (int $product_import_id) : bool;

	function changeProductImportsTrigger (array $product_imports) : void;

    //Exports
    //$pragma_entity_id - id сущности в базе данных pragma_crm
    function createPragmaExport (int $pragma_entity_id, int $product_id, float $quantity, float $selling_price) : iExport;

    function getExport (int $export_id) : iExport;

    function deleteExport (int|array $exports_id) : bool;

	function changeEntitiesTrigger() : void;
}