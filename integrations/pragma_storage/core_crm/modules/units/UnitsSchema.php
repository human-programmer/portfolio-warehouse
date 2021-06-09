<?php


namespace PragmaStorage;
require_once __DIR__ . '/../../../CONSTANTS.php';


class UnitsSchema extends PragmaStoreDB
{
    private int $pragma_account_id;

    protected function __construct(int $pragma_account_id)
    {
        parent::__construct();
        $this->pragma_account_id = $pragma_account_id;
    }

    function getUnit(): array
    {
        return $this->SQL();
    }


    function SQL(): array
    {
        $pragma_account_id = $this->getPragmaAccountId();

        $products_schema = $this->getStorageUnits();

        $sql = "SELECT $products_schema FROM products WHERE account_id = $pragma_account_id GROUP BY unit";
echo $sql;
        return self::query($sql);

    }


    public function getPragmaAccountId(): int
    {
        return $this->pragma_account_id;
    }

}