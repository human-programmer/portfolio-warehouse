<?php


namespace PragmaStorage;


use Generals\Functions\Date;

require_once __DIR__ . '/ATargetCatalog.php';

class ProductsImportsCatalog extends ATargetCatalog {
    function getProductsImports(): array {
        $sql = self::createSql();
        $models = self::querySql($sql);
        return self::formatting($models);
    }

    function createSql(): string {
        $products = self::getStorageProductsSchema();

        $productsImportsSchema = $this->productsImportsQuery();
        $alias = 'pi';

        return "SELECT
                    $products.`id` as `product_id`,
                    $products.`category_id`,
                    $products.`article`,
                    $products.`title`,
                    $products.`selling_price`,
                    $products.`deleted`,
                    
                    $alias.id,
                    $alias.import_id,
                    $alias.quantity,
                    $alias.free_balance,
                    $alias.purchase_price,
                    $alias.balance,
                    $alias.source,
                    $alias.date_create
                FROM $products
                    INNER JOIN ($productsImportsSchema) $alias ON $alias.product_id = $products.id
                WHERE 1";
    }

    protected function productsImportsQuery(): string {
        $products_imports = self::getStorageProductImportsSchema();
        $condition = $this->createProductsImportsCondition();
        return "SELECT 
                    $products_imports.id,
                    $products_imports.product_id,
                    $products_imports.import_id,
                    $products_imports.quantity,
                    $products_imports.free_balance,
                    $products_imports.purchase_price,
                    $products_imports.balance,
                    $products_imports.source,
                    $products_imports.date_create
                FROM $products_imports
                WHERE $condition";
    }

    protected function createProductsImportsCondition(): string {
        $conditions = self::filterConditions([
            $this->createProductImportsIdCondition(),
            $this->createProductsIdCondition(),
            $this->createAllImportsIdCondition()
        ]);
        return implode(' AND ', $conditions);
    }

    private function createAllImportsIdCondition(): string {
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
            $this->createImportsIdCondition(),
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

    private function createProductsIdCondition(): string {
        $products_imports = self::getStorageProductImportsSchema();
        return self::createTargetIdCondition("$products_imports.product_id", $this->filter->fetchProductsId());
    }

    private function createProductImportsIdCondition(): string {
        $products_imports = self::getStorageProductImportsSchema();
        return self::createTargetIdCondition("$products_imports.id", $this->filter->fetchId());
    }

    protected static function formatting(array $models): array {
        foreach($models as &$model) {
            $model['selling_price'] = (float) $model['selling_price'];
            $model['free_balance'] = (float) $model['free_balance'];
            $model['balance'] = (float) $model['balance'];
            $model['quantity'] = (float) $model['quantity'];
            $model['purchase_price'] = (float) $model['purchase_price'];
            $model['date_create'] = Date::getIntTimeStamp($model['date_create']);
        }
        return $models;
    }
}