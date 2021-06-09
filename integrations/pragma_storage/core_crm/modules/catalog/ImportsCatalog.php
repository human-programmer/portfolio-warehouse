<?php


namespace PragmaStorage;


require_once __DIR__ . '/ATargetCatalog.php';

class ImportsCatalog extends ATargetCatalog {
    function getImports(): array {
        $sql = $this->getSql();
        return self::querySql($sql);
    }

    private function getSql(): string {
        $condition = self::createConditionString();
        $imports = self::getStorageImportsSchema();
        $order = $this->createOrderCondition();
        return "SELECT 
                   $imports.`id`, 
                   $imports.`store_id`, 
                   $imports.`number`, 
                   $imports.`provider`, 
                   $imports.`date`,
                   $imports.`date_create`
                FROM $imports
                WHERE $condition $order";
    }

    private function createConditionString(): string {
        $conditions = self::createConditions();
        $conditions = self::filterConditions($conditions);
        return implode(' AND ', $conditions);
    }

    private function createConditions(): array {
        return [
            $this->createStoresCondition(),
            $this->createImportsIdCondition(),
            $this->createDateCondition(),
        ];
    }

    private function createStoresCondition(): string {
        $store_str = implode(',', $this->filter->fetchStoreId());
        $stores = self::getStorageStoresSchema();
        $imports = self::getStorageImportsSchema();
        $condition = $store_str ? "AND $stores.id IN ($store_str)" : '';
        return "$imports.store_id IN(SELECT id FROM $stores WHERE $stores.account_id = $this->account_id $condition)";
    }

    private function createImportsIdCondition(): string {
        $imports = self::getStorageImportsSchema();
        return self::createTargetIdCondition("$imports.id", $this->filter->fetchId());
    }

    private function createDateCondition(): string {
        $imports = self::getStorageImportsSchema();
        return $this->filter->getDate("$imports.date");
    }

    private function createOrderCondition(): string {
        $imports = self::getStorageImportsSchema();
        return $this->filter->getOrder("$imports.date");
    }
}