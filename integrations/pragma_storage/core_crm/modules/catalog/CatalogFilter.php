<?php


namespace PragmaStorage;


use Generals\Functions\Date;

class CatalogFilter {
    function __construct(private array $filter) {}

    function fetchStoreId(): array {
        return self::fetchFilterField($this->filter, 'store_id');
    }


    function fetchCategoryId(): array {
        return self::fetchFilterField($this->filter, 'category_id');
    }

    function fetchImportsId(): array {
        return self::fetchFilterField($this->filter, 'import_id');
    }

    function fetchProductsId(): array {
        return self::fetchFilterField($this->filter, 'product_id');
    }

    function fetchId(): array {
        return self::fetchFilterField($this->filter, 'id');
    }

    function fetchSearch(): string {
        return $this->fetchStringField('search');
    }

    function fetchStringField(string $field_name): string {
        if(!isset($this->filter[$field_name]) || !trim($this->filter[$field_name])) return '';
        return trim($this->filter[$field_name]);
    }

    private static function fetchFilterField(array $filter, string $field_name): array {
        if(!isset($filter[$field_name])) return [];
        $arr = is_array($filter[$field_name]) ? $filter[$field_name] : [$filter[$field_name]];
        foreach($arr as $id)
            $result[] = (int) $id;
        return $result ?? [];
    }

    function getOrder(string $fieldName): string {
        $order = $this->filter['order'] ?? '';
        $order = strtoupper(trim($order));
        if ($order !== 'DESC' && $order !== 'ASC')
            $order = 'DESC';
        return " ORDER BY $fieldName $order";
    }

    function getDate(string $fieldName): string {
        $date = $this->filter['date'] ?? [];

        if (!isset($date['start']) && !isset($date['end'])) return '';

        $start = self::toDate($date['start'] ?? 0);
        $end = self::toDate($date['end'] ?? time());

        return "$fieldName BETWEEN '$start' AND '$end'";
    }

    private static function toDate(int $time): string {
        return Date::getStringTimeStamp($time);
    }
}