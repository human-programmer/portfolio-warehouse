<?php

namespace market;

use JetBrains\PhpStorm\Pure;

require_once __DIR__ . '/../../business_rules/validation/iValidation.php';
require_once __DIR__ . '/ValidationSchema.php';
require_once __DIR__ . '/ValidationDB.php';

class Validation implements iValidation
{
    protected ValidationSchema $GENERAL;
    /**
     * @var ValidationDB
     */
    private ValidationDB $GENERALDB;

    public function __construct(private int $phone)
    {
        $this->GENERAL = new ValidationSchema($phone);
        $this->GENERALDB = new ValidationDB();
    }
    
    function addPhoneDB(string $email):void{
        $this->GENERALDB->addUserPhone($this->phone, $email);
    }

    function createCode(): void
    {
        $code = $this->GENERATOR_CODE();
        $this->GENERAL->addCode($code);
    }

    function deleteCode(): void
    {
        $this->GENERAL->deleteCode();
    }

    function getCode(): int
    {
        return $this->GENERAL->checkCode();
    }

    #[Pure] private function GENERATOR_CODE(): int
    {
        return mt_rand(1001, 9999);
    }


    function checkCode(int $code): int
    {
        $codeInDb = $this->getCode();
        return $code == $codeInDb? 1:0;
    }
}