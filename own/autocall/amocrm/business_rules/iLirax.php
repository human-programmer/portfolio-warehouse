<?php


namespace Autocall\Amocrm;


interface iLirax extends \Autocall\Pragma\iLirax
{
    function getIdPipelineByPhone(int $Phone) : int;
    function getIdResponsibleLead(int $Phone) : int;


}