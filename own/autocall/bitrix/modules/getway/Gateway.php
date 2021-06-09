<?php


namespace Autocall\Bitrix;


class Gateway extends \RestApi\Bitrix24\Bitrix24Gateway implements iGateway
{

    function getIncludeNumber(int $id): iIncludeNumber
    {
        $IncludeNumberModel = $this->getIncludeNumberModel($id);
        return $this->createIncludeNumberModel($IncludeNumberModel);
    }

    function getIncludeNumberModel(int $id): array
    {

        $params = array(
            "filter" => array("ID" => $id),
            "select" => array("ID", "NAME", "LAST_NAME", "UF_PHONE_INNER")
        );
        return $this->query('user.get', $params)['result'][0];
    }

    function createIncludeNumberModel(array $IncludeNumberModel): iIncludeNumber
    {
        $innerNumber = $IncludeNumberModel['UF_PHONE_INNER'] * 1;
        return new IncludeNumber($innerNumber);
    }




    function getPipeline(): iPipeline
    {
        // TODO: Implement getPipeline() method.
    }

    function getPipelineModels(): array
    {
        $params = [];
        $method = 'crm.dealcategory.list';
        return $this->query($method, $params)['result'];
    }


    function getContact(int $id): iContact
    {
        $contactModel = $this->getContactModel($id);
        return $this->createContact($contactModel);
    }

    private function getContactModel(int $id): array
    {
        $params = ['id' => $id];
        $method = '';
        return $this->query($method, $params);
    }

    private function createContact(array $contactModel): iContact
    {
        $name = $this->searchNumberAndUpdate($contactModel['name']);
        $Phone = $contactModel['custom_fields_values'][0]['values'] * 1;
        return new Contact($name, $Phone);
    }


    function getLeads(int $id): iLead
    {
        $leadModel = $this->getLeadsModel($id);
        return $this->createLeads($leadModel);
    }

    private function getLeadsModel(int $id): array
    {


    }

    private function createLeads(array $leadModel): iLead
    {
        $id = $leadModel['id'] * 1;
        $name = $this->searchNumberAndUpdate($leadModel['name']);
        return new Lead($id, $name);
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


}