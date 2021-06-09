<?php


namespace Autocall\Bitrix;


interface iBitrix extends \Autocall\Pragma\iLirax
{
    function getIdPipelineByPhone(int $Phone) : int;
    function getIdResponsibleLead(int $Phone) : int;
}