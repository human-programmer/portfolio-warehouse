<?php


namespace Autocall\Bitrix;


require_once __DIR__ . '../business_rules/iBitrix.php';


class Bitrix extends \Autocall\Pragma\Lirax implements iBitrix
{
    function getIdPipelineByPhone(int $Phone) : int{

    }
    function getIdResponsibleLead(int $Phone) : int{

    }

}