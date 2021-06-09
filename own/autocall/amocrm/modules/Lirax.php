<?php

namespace Autocall\Amocrm;

require_once __DIR__ . '/../business_rules/iLirax.php';

class Lirax extends \Autocall\Pragma\Lirax implements iLirax
{

    function getIdPipelineByPhone(int $Phone): int
    {
        $CONTACT = Factory::getGateway()->getContactByPhone($Phone);
        $LEADS_ID = $CONTACT->getLeadByContactId();
        $LEAD = Factory::getGateway()->getLeads($LEADS_ID);
        return $LEAD->getIdPipeline();
    }


    function getIdResponsibleLead(int $Phone): int
    {
        $CONTACT = Factory::getGateway()->getContactByPhone($Phone);
        $LEADS_ID = $CONTACT->getLeadByContactId();
        $LEAD = Factory::getGateway()->getLeads($LEADS_ID);
        return $LEAD->getIdResponsible();
    }

    function executeTask(int $id): void
    {


    }
}