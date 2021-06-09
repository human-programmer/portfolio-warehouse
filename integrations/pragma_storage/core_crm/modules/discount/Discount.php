<?php


namespace PragmaStorage;

require_once __DIR__ . '/../../business_rules/discount/iDiscount.php';
require_once __DIR__ . '/DiscountSchema.php';

class Discount extends DiscountSchema implements iDiscount
{

    function __construct(int $pragma_account_id)
    {
        parent::__construct($pragma_account_id);
    }

    function saveDiscount(int $id, int $discount, float $full_price): int
    {
        return $this->CreateUpdateDiscount($id, $discount, $full_price);
    }

    function getDiscounts(): array
    {
        return $this->getDiscountSql();
    }

    function getDiscountOnId(int $id): array
    {
        $answer = [];
        $res = $this->getDiscountIdSql($id);
        $answer['discount'] = empty($res) ? 0 : intval($res[0]['discount']);
        $answer['full_price'] = empty($res) ? 0 : intval($res[0]['full_price']);
        return $answer;
    }

    function deleteDiscount(int $product_export_id): void
    {
         $this->deleteSql($product_export_id);
    }

}