<?php


namespace PragmaStorage;


interface iCatalog
{
    function getStoreModels () : array;

	/*
	 * filter:
	 * `store_id`
	 */
    function getCategoriesModels (array $filter = []) : array;

    /*
     * filter:
     * `id`,
     * `category_id`,
     * `search`,
     * `with`: ['balance']
     */
    function getProductModels (array $filter = []) : array;

    /*
     * filter:
     * `id`,
     * `category_id`,
     * `product_id`,
     * `store_id`,
     */
    function getProductDeficitModels (array $filter = []) : array;

    /*
     * filter:
     * `id`,
     * `store_id`,
     * `date`: ['start' => timestamp, 'end' => timestamp],
     * `order` (date order): 'asc' or 'desc'
     */
    function getImportModels (array $filter = []) : array;

    /*
     * filter:
     * `id`
     * `store_id`,
     * `import_id`,
     * `product_id`,
     */
    function getProductImportModels (array $filter = []) : array;

    /*
     * filter:
     * `id`,
     * `store_id`,
     * `import_id`,
     * `product_id`,
     * `status_id`,
     * `user_id`,
     * `date`: ['start' => timestamp, 'end' => timestamp],
     */
    function getExportModels (array $filter = []);

    function getPragmaEntityExportModels (int $pragma_entity_id) : array;

    function getUnits():array;
}