<?php

namespace Autocall\Amocrm;

require_once __DIR__ . '/../Factory.php';


class Hook
{
    private int $AccountId;
    private int $LeadsId;
    private int $PipelinesId;
    private string $subdomain;
    static private iContact $CONTACT;
    static private iLead $LEAD;
    static private iCompanies $COMPANIES;
    static private iCustomers $CUSTOMERS;

    public function __construct($REQUEST, string $subdomain, $logger)
    {
        $this->subdomain = $subdomain;
        $this->AccountId = intval($REQUEST['account_id']);
        $this->LeadsId = intval($REQUEST['event']['data']['id']);
        $this->PipelinesId = intval($REQUEST['event']['data']['pipeline_id']);
        Factory::amocrmInit($this->subdomain, $logger);
    }


    function run(): void
    {
        Factory::getLiraxSettings()->setPipelineId($this->PipelinesId);

        $LEAD = self::getLead($this->LeadsId);
        $TitleLead = $LEAD->getName();
        $ContactId = $LEAD->getIdContact();
        $CONTACT = self::getContact($ContactId);
        $PhoneNumber = $CONTACT->getPhone();
        if ($PhoneNumber < 8) {
            Factory::getLogWriter()->add('phone', 0);
            die();
        }

        $NUMBERAPD = self::searchNumberANDUPD($PhoneNumber);
        $speech = "Осуществляем звонок по новой заявке $TitleLead на номер $NUMBERAPD";
        Hook::_call_($PhoneNumber, $speech);

    }

    static function getLead($LeadsId): iLead
    {
        return Factory::getGateway()->getLeads($LeadsId);
    }

    static function getContact(int $ContactId): iContact
    {
        return Factory::getGateway()->getContact($ContactId);
    }

    static function getCompanies(int $CompaniesId): iCompanies
    {
        return Factory::getGateway()->getCompanies($CompaniesId);
    }

    static function getCustomers(int $CustomersId): iCustomers
    {
        return Factory::getGateway()->getCustomers($CustomersId);
    }

    static function AutoTaskInit(string $subdomain, int $element_type, int $element_id, int $task_id, $logger): void
    {
        Factory::amocrmInit($subdomain, $logger);
        switch ($element_type) {
            case 1:
                self::AutoTask_Contact($element_id);
                break;
            case 2:
                self::AutoTask_Lead($element_id);
                break;
            case 3:
                self::AutoTask_Companies($element_id);
                break;

            case 12:
                self::AutoTask_Customers($element_id);
                break;

        }
        Hook::call_(self::$LEAD, self::$CONTACT);
        Factory::getGateway()->performTask($task_id, 'Виджет AutoTask');

    }
    static function AutoTask_ZERO_init(string $subdomain, int $phone, int $task_id, $logger):void
    {
        Factory::amocrmInit($subdomain, $logger);
        $NUMBERAPD = self::searchNumberANDUPD($phone);
        $speech = "Виджет Авто Таск Осуществляем звонок по на номер $NUMBERAPD";
        if ($phone < 8) {
            Factory::getLogWriter()->add('phone', 0);
            die();
        }
        Factory::getLogWriter()->add('$speech, $PhoneNumber', [$speech, $phone]);
        self::_call_($phone, $speech);
        Factory::getGateway()->performTask($task_id, 'Виджет AutoTask');

    }


    static function AutoTask_Lead(int $leads_id): void
    {
        self::$LEAD = self::getLead($leads_id);
        $ContactId = self::$LEAD->getIdContact();
        self::$CONTACT = self::getContact($ContactId);
    }

    static function AutoTask_Contact(int $contact_id): void
    {
        self::$CONTACT = self::getContact($contact_id);
        $leads_id = self::$CONTACT->getLeadByContactId();
        self::$LEAD = self::getLead($leads_id);
    }

    static function AutoTask_Companies(int $companies_id): void
    {
        self::$COMPANIES = self::getCompanies($companies_id);
        $contact_id = self::$COMPANIES->getIdContact();
        self::$CONTACT = self::getContact($contact_id);
        $leads_id = self::$CONTACT->getLeadByContactId();
        self::$LEAD = self::getLead($leads_id);
    }

    static function AutoTask_Customers(int $customer_id): void
    {
        self::$CUSTOMERS = self::getCustomers($customer_id);
        $ContactId = self::$CUSTOMERS->getIdContact();
        self::$CONTACT = self::getContact($ContactId);
        $leads_id = self::$CONTACT->getLeadByContactId();
        self::$LEAD = self::getLead($leads_id);
    }


    static function call_(iLead $LEAD, iContact $CONTACT): void
    {
        $pipeline_id = $LEAD->getIdPipeline();
        Factory::getLiraxSettings()->setPipelineId($pipeline_id);
        $TitleLead = $LEAD->getName();
        $PhoneNumber = $CONTACT->getPhone();
        $NUMBERAPD = self::searchNumberANDUPD($PhoneNumber);
        $speech = "Виджет Авто таск Осуществляем звонок по $TitleLead на номер $NUMBERAPD";
        if ($PhoneNumber < 8) {
            Factory::getLogWriter()->add('phone', 0);
            die();
        }
        Factory::getLogWriter()->add('$speech, $PhoneNumber', [$speech, $PhoneNumber]);
        self::_call_($PhoneNumber, $speech);
    }


    static function _call_($PhoneNumber, $speech)
    {
        Factory::getLiraxCore($PhoneNumber)->setMode(true);
        Factory::getLirax()->getLiraxSettingsStruct()->setSpeech($speech);
        Factory::getLirax()->getLiraxSettingsStruct()->setTargetNumber($PhoneNumber);
        Factory::getLirax()->call();
    }


    static function searchNumberANDUPD($str): string
    {
        $newStr = '';
        $pieces = explode(" ", $str);
        foreach ($pieces as $item) {
            if (preg_match('([0-9-]+)', $item)) {
                $newStr .= self::PhoneD($item);
            } else {
                $newStr .= $item . ' ';
            }
        }
        return $newStr;
    }

    static function PhoneD(string $str): string
    {
        $newStr = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $dit = $str[$i];
            switch ($i) {
                case 2:
                case 4:
                case 7:
                case 9:
                    $newStr .= $dit . " ";
                    break;
                default:
                    $newStr .= $dit;
                    break;
            }
        }
        return $newStr;
    }
}