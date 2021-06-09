<?php


namespace PragmaStorage;


require_once __DIR__ . '/CatalogFilter.php';

abstract class ATargetCatalog extends PragmaStoreDB {
    protected CatalogFilter $filter;

    function __construct(protected int $account_id, array $filter) {
        parent::__construct();
        $this->filter = new CatalogFilter($filter);
    }

    protected static function createTargetIdCondition(string $field_name, array $ids): string {
        $str_val = implode(',', $ids);
        if(!$str_val) return '';
        return $str_val ? "$field_name IN ($str_val)" : '';
    }

    protected static function ifNullThenNull(string $field): string {
        return "CASE WHEN $field IS NULL THEN 0 ELSE $field END";
    }

    protected static function filterConditions(array $conditions): array {
        foreach($conditions as $condition)
            if($condition)
                $result[] = $condition;
        return $result ?? [];
    }

    protected static function createSearch(string $field_name, string $val): string {
        return "LOWER($field_name) LIKE '%" . strtolower($val) . "%'";
    }
}