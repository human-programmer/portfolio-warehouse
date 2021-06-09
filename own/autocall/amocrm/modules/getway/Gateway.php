<?php

namespace Autocall\Amocrm;


use Services\General\iNode;

require_once __DIR__ . '/../../business_rules/getway/iGateway.php';
require_once __DIR__ . '/../../modules/getway/Lead.php';
require_once __DIR__ . '/../../modules/getway/Contact.php';
require_once __DIR__ . '/../../modules/getway/Companies.php';
require_once __DIR__ . '/../../modules/getway/Customers.php';
require_once __DIR__ . '/../../../../../lib/rest_api/amocrm/AmocrmGateway.php';


class Gateway extends \RestApi\Amocrm\AmocrmGateway implements iGateway
{

    public function __construct(iNode $node, \LogWriter $log_writer)
    {
        parent::__construct($node, $log_writer);
    }

    function getContactByPhone(int $Phone): iContact
    {
        $ContactModel = $this->getContactModelByPhone($Phone);
        Factory::getLogWriter()->add('$ContactModel', $ContactModel);
        $name = $this->UpdateName($ContactModel['name']);
        $SET = isset($ContactModel['_embedded']['leads'][0]);
        $AttachmentLeadId = $SET ? intval($ContactModel['_embedded']['leads'][0]['id']) : 0;
        return new Contact($name, $Phone, $AttachmentLeadId);
    }

    function getResponsibleIdLead(int $Phone): iLead
    {
        $Contact = $this->getContactByPhone($Phone);
        $idLeads = $Contact->getLeadByContactId();
        return $this->getLeads($idLeads);
    }


    private function getContactModelByPhone(int $Phone): array
    {
        $params = [
            'with' => 'leads',
            'query' => $Phone
        ];
        return $this->contacts($params, 'GET')['_embedded']['contacts'][0];
    }

    private function getContactModel(int $id): array
    {
        $params = [
            'with' => 'leads',
            'id' => $id
        ];
        return $this->contacts($params, 'GET')['_embedded']['contacts'][0];
    }

    private function createContact(array $contactModel): iContact
    {
        $name = $this->UpdateName($contactModel['name']);
        $Phone = $this->getNumber($contactModel['custom_fields_values']);

        $DataLead = isset($contactModel['_embedded']['leads'][0]);
        $AttachmentLeadId = $DataLead ? intval($contactModel['_embedded']['leads'][0]['id']) : 0;
        return new Contact($name, $Phone, $AttachmentLeadId);
    }

    private function getNumber(array $mas): int
    {
        $phone_number = 0;
        if (count($mas) > 0) {
            foreach ($mas as $item) {
                if ($item['field_code'] == "PHONE") {
                    $phone_number = preg_replace('/[^0-9]/', '', $item['values'][0]['value']);
                    break;
                }
            }
        }
        return $phone_number;
    }


    function getLeads(int $id): iLead
    {
        $leadModel = $this->getLeadsModel($id);
        return $this->createLeads($leadModel);
    }

    private function getLeadsModel(int $id): array
    {
        $params = ['id' => $id, 'with' => 'contacts'];
        return $this->leads($params, 'GET')['_embedded']['leads'][0];
    }

    private function getCompaniesModel(int $id): array
    {
        $params = ['id' => $id, 'with' => 'contacts'];
        return $this->companies($params, 'GET')['_embedded']['companies'][0];
    }

    private function getCustomersModel(int $id): array
    {
        $params = ['id' => $id, 'with' => 'contacts'];
        return $this->customers($params, 'GET')['_embedded']['customers'][0];
    }

    private function createLeads(array $leadModel): iLead
    {
        $NAME = $leadModel['name'];
        $id_contact = $leadModel['_embedded']['contacts'][0]['id'] * 1;
        $id_pipeline = $leadModel['pipeline_id'] * 1;
        $id_responsible = $leadModel['responsible_user_id'] * 1;
        $name = $this->UpdateName($NAME);

        return new Lead($id_contact, $name, $id_pipeline, $id_responsible);
    }

    private function createCompanies(array $CompaniesModel): iCompanies
    {
        $id_contact = $CompaniesModel['_embedded']['contacts'][0]['id'] * 1;
        return new Companies($id_contact);
    }

    private function createCustomers(array $CustomersModel): iCustomers
    {
        $id_contact = $CustomersModel['_embedded']['contacts'][0]['id'] * 1;
        return new Customers($id_contact);
    }


    private function UpdateName(string $str): string
    {
        $arr = ["#", "/"];
        return trim(str_replace($arr, "", $str));
    }


    private function searchNumberAndUpdate($str): string
    {
        $newStr = '';
        $pieces = explode(" ", $str);
        foreach ($pieces as $item) {
            if (preg_match('([0-9-]+)', $item)) {
                $newStr .= $this->PhoneD($item);
            } else {
                $newStr .= $item . ' ';
            }
        }
        return $newStr;

    }

    private function PhoneD(string $str): string
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


    function getContact(int $id): iContact
    {
        $contactModel = $this->getContactModel($id);
        Factory::getLogWriter()->add('$contactModel', $contactModel);
        return $this->createContact($contactModel);
    }


    function performTask(int $id, string $text): void
    {
        $this->request_performTask($id, $text);
    }

    private function request_performTask(int $id, string $text): void
    {
        $params = [
            'id' => $id,
            'is_completed' => true,
            'result' => [
                "text" => $text
            ]
        ];
        $this->tasks([$params], 'PATCH');
    }

    function getCompanies(int $id): iCompanies
    {
        $ContactModel = $this->getCompaniesModel($id);
        return $this->createCompanies($ContactModel);
    }

    function getCustomers(int $id): iCustomers
    {
        $CustomersModel = $this->getCustomersModel($id);
        return $this->createCustomers($CustomersModel);

    }
}