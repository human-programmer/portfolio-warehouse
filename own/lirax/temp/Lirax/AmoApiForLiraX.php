<?php

require_once __DIR__ . '/../../../../lib/services/Factory.php';
require_once __DIR__ . '/../../../../lib/rest_api/amocrm/AmocrmGateway.php';


class AmoApiForLiraX
{
    private \RestApi\Amocrm\AmocrmGateway $cliAMO;
    private string $widget_code = 'pmLirax';

    private int $account_id;

    public function __construct(int $account_id)
    {
        $this->account_id = $account_id;
        $module = $this->getAccountsModule();
        $this->cliAMO = new \RestApi\Amocrm\AmocrmGateway($module);
    }

    private function getAccountsModule(): \Services\General\iNode
    {
        \Services\Factory::init($this->widget_code, '', '');
        return \Services\Factory::getNodesService()->findAmocrmNodeAccId($this->widget_code, $this->account_id);
    }

    function search_number(int $id_leads): array
    {
        $data = $this->cliAMO->leads(['id' => $id_leads, 'with' => 'contacts'], 'GET')['_embedded'];
        $id_contact = $data['leads'][0]["_embedded"]['contacts'][0]["id"];
        $name_leads = $data['leads'][0]['name'];
        $data_phone = $this->return_phones_contact($id_contact);
        $phone = $data_phone['phone'];
        $name_phone = $data_phone['name'];
        return ['name_leads' => $name_leads, "phone" => $phone * 1, "name_phone" => $name_phone];

    }


    private function return_phones_contact($id): array
    {
        $data = $this->cliAMO->contacts(['id' => $id], 'GET');
        $set = isset($data['_embedded']['contacts'][0]['custom_fields_values'][0]['values']);
        $name = $set ? $data['_embedded']['contacts'][0]['name'] : 0;
        $Phone = $set ? $data['_embedded']['contacts'][0]['custom_fields_values'][0]['values'] : 0;
        return $set ? ['phone' => intval($Phone[0]['value']), 'name' => $name] : [0];
    }


    function search_responsible_leads(int $phone)
    {
        $params = [
            'with' => 'leads',
            'query' => $phone

        ];
        $res = $this->cliAMO->contacts($params, 'GET');
        $res = $res['_embedded']['contacts'][0];
        return $res['responsible_user_id'];
    }

    function id_pip_on_lead(int $phone): int
    {
        $id_leads = $this->search_leads($phone);
        return $this->get_id_pipeline($id_leads);
    }

    function search_leads(int $phone): int
    {
        $params = [
            'with' => 'leads',
            'query' => $phone

        ];
        $res = $this->cliAMO->contacts($params, 'GET');
        $res = $res['_embedded']['contacts'][0];
        $SET = isset($res['_embedded']['leads'][0]);
        return $SET ? intval($res['_embedded']['leads'][0]['id']) : 0;

    }

    function get_id_pipeline(int $id_leads): int
    {
        $res = $this->cliAMO->leads(['id' => $id_leads], 'GET');
        $exist_pipeline = isset($res['_embedded']['leads'][0]['pipeline_id']);
        return $exist_pipeline ? intval($res['_embedded']['leads'][0]['pipeline_id']) : 0;
    }


}