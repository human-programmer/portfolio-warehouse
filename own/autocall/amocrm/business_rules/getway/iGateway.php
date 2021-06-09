<?php


namespace Autocall\Amocrm;


interface iGateway
{

    function getLeads(int $id): iLead; // get Id_contact & get LeadsName

    function getContact(int $id): iContact; // get Name  & Phone

    function getContactByPhone(int $Phone):iContact;

    function getResponsibleIdLead(int $Phone):iLead;

    function performTask(int $id, string $text): void;

    function getCompanies(int $id): iCompanies;

    function getCustomers(int $id): iCustomers;


}