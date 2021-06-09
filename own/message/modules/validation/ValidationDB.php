<?php


namespace market;
require_once __DIR__ . '/../../../../lib/db/CRMDB.php';


use Generals\CRMDB;

class ValidationDB extends CRMDB
{
    public function __construct()
    {
        parent::__construct();
    }

    function addUserPhone(int $phone, string $email): void
    {
        $module_schema = self::getUsersSchema();
        $_email = $email;
        $sql = "UPDATE $module_schema 
                SET 
                    $module_schema.`phone` = :phone
                WHERE $module_schema.`email` = :email";

        self::execute($sql, ['phone' => $phone, 'email' => $email]);

    }

}