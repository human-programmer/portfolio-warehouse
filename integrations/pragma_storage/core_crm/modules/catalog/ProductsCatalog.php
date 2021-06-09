<?php

namespace PragmaStorage;

require_once __DIR__ . '/ATargetCatalog.php';

class ProductsCatalog extends ATargetCatalog {

    function getProducts(): array {
        $sql = self::createSql();
        $models = self::querySql($sql);
        return self::formatting($models);
    }

    function createSql(): string {
        $products = self::getStorageProductsSchema();
        $condition = $this->createCondition();

        $productsImportsSchema = $this->productsImportsQuery();
        $alias = 'pi';

        $free_balance = self::ifNullThenNull("$alias.`free_balance`");
        $balance = self::ifNullThenNull("$alias.`balance`");


        return "SELECT
                    $products.`id`,
                    $products.`category_id`,
                    $products.`article`,
                    $products.`title`,
                    $products.`selling_price`,
                    $products.`deleted`,
                    SUM($free_balance) as free_balance,
                    SUM($balance) as balance
                FROM $products
                    LEFT JOIN ($productsImportsSchema) $alias ON $alias.product_id = $products.id
                WHERE $condition
                GROUP BY $products.`id`";
    }

    protected function productsImportsQuery(): string {
        $products_imports = self::getStorageProductImportsSchema();
        $condition = $this->createProductsImportsCondition();
        return "SELECT 
                    $products_imports.product_id,
                    $products_imports.free_balance,
                    $products_imports.balance
                FROM $products_imports
                WHERE $condition";
    }

    protected function createProductsImportsCondition(): string {
        $conditions_str = $this->createProductsImportsConditionString();
        $product_imports = self::getStorageProductImportsSchema();
        $imports = self::getStorageImportsSchema();
        $stores = self::getStorageStoresSchema();
        $condition = $conditions_str ? "AND $conditions_str" : '';
        return "$product_imports.import_id IN (
            SELECT $imports.id FROM $imports WHERE $imports.store_id IN(
                SELECT id FROM $stores WHERE $stores.account_id = $this->account_id $condition)
            )";
    }

    protected function createProductsImportsConditionString(): string {
        $conditions = self::filterConditions($this->createProductsImportsConditions());
        return implode(' AND ', $conditions);
    }

    protected function createProductsImportsConditions(): array {
        return [
            $this->createImportsOfStoresCondition(),
            $this->createImportsIdCondition()
        ];
    }

    protected function createImportsOfStoresCondition(): string {
        $imports = self::getStorageImportsSchema();
        return self::createTargetIdCondition("$imports.store_id", $this->filter->fetchStoreId());
    }

    protected function createImportsIdCondition(): string {
        $imports = self::getStorageImportsSchema();
        return self::createTargetIdCondition("$imports.id", $this->filter->fetchImportsId());
    }

    protected function createCondition(): string {
        $conditions = $this->createAllConditions();
        $conditions = self::filterConditions($conditions);
        return implode(' AND ', $conditions);
    }

    protected function createAllConditions(): array {
        return [
            $this->createAccountCondition(),
            $this->createProductsIdCondition(),
            $this->createCategoriesCondition(),
            $this->createSearchFieldCondition(),
        ];
    }

    private function createAccountCondition(): string {
        $products = self::getStorageProductsSchema();
        return "$products.account_id = $this->account_id";
    }

    private function createCategoriesCondition(): string {
        $dif_condition = $this->createDiffStoresAndCategoriesCondition();
        if(!$dif_condition) return '';
        $categories_to_stores = self::getStorageCategoriesToStoresSchema();
        $products = self::getStorageProductsSchema();
        return "$products.category_id IN (SELECT $categories_to_stores.category_id FROM $categories_to_stores WHERE $dif_condition)";
    }

    private function createDiffStoresAndCategoriesCondition(): string {
        $store_id_str = self::createTargetIdCondition('store_id', $this->filter->fetchStoreId());
        $catalog_id_str = self::createTargetIdCondition('category_id', $this->filter->fetchCategoryId());
        if(!$store_id_str && !$catalog_id_str) return '';
        return ($catalog_id_str && $store_id_str) ? "$catalog_id_str AND $store_id_str" : "$catalog_id_str $store_id_str";
    }

    private function createProductsIdCondition(): string {
        $products = self::getStorageProductsSchema();
        return self::createTargetIdCondition("$products.id", $this->filter->fetchId());
    }

    private function createSearchFieldCondition(): string {
        $products = self::getStorageProductsSchema();
        $search = $this->filter->fetchSearch();
        if(!$search) return '';
        $as_title = self::createSearch("$products.title", $search);
        $as_article = self::createSearch("$products.article", $search);
        return "($as_article OR $as_title)";
    }

    protected static function formatting(array $models): array {
        foreach($models as &$model) {
            $model['selling_price'] = (float) $model['selling_price'];
            $model['free_balance'] = (float) $model['free_balance'];
            $model['balance'] = (float) $model['balance'];
        }
        return $models;
    }
}