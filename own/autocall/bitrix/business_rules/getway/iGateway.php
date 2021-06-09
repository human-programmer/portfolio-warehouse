<?php


namespace Autocall\Bitrix;


interface iGateway
{
    function getLeads(int $id): iLead; // get Id_contact & get LeadsName

    function getContact(int $id): iContact; // get Name  & Phone

    function getPipeline(): iPipeline;

    function getIncludeNumber(int $id): iIncludeNumber;



}