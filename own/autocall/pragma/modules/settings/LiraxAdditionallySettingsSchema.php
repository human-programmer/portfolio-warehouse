<?php


namespace Autocall\Pragma;


class liraxAdditionallySettingsSchema
{
    private $DB;
    private int $id;

    public function __construct(int $pragma_account_id)
    {
        $json = file_get_contents(__DIR__ . '/../../../db/dbAdditionally.json');
        $this->DB = json_decode($json, true);
        $this->id = $pragma_account_id;
        $this->init();
    }


    function getGeneralSettingsModel(): array
    {
        return $this->DB[$this->id];
    }


    function setTimeResponsible(int $quantity)
    {
        $this->DB[$this->id]['TimeResponsible']=$quantity;

    }

    function setWorkTime(array $quantity)
    {
        $this->DB[$this->id]['WorkTime'] = $quantity;
    }

    function setQuantityCallClient(int $quantity)
    {
        $this->DB[$this->id]['QuantityCallClient'] = $quantity;
    }

    function setNumberOfCallAttempts(array $array_calls)
    {
        $this->DB[$this->id]['NumberOfCallAttempts'] = $array_calls;
    }

    function setArrayUsePipelineShops(array $array_pipeline)
    {
        $account_id = $this->id;
        $new_arr =  $this->update_array($account_id,$array_pipeline);
        $this->DB[$this->id]['ArrayUsePipelineShops'] = $new_arr;
    }

    function setArrayUsePipelineNumbers(array $array_pipeline)
    {
        $this->DB[$this->id]['ArrayUsePipelineNumbers'] = $array_pipeline;
    }
    function setArrayUsePriority(array $array_pipeline)
    {
        $this->DB[$this->id]['ArrayUsePriority'] = $array_pipeline;
    }

    private function update_array(int $account_id, array $pipelines): array
    {
        $data = [];
        foreach ($pipelines as $item => $value) {
            array_push($data, array(
                'id_account' => $account_id,
                'name_pipeline' => trim(urldecode($value['pip_name'])),
                'id_pipeline' => intval($value['pip_id']),
                'id_set_pep' => intval($value['pip_set_id']),
            ));
        }
        return $data;
    }



    private function init()
    {
        if (!empty($this->DB)) {
            $keys = array_keys($this->DB);
            switch (in_array($this->id, $keys)) {
                case false:
                    $this->create();
                    break;
                default:
                    break;
            }
        } else {
            $this->create();
        }
    }


    private function create()
    {
        $this->DB[$this->id] = array(
            'TimeResponsible' => '',
            'WorkTime' => [],
            'QuantityCallClient' => [],
            'NumberOfCallAttempts' => [],
            'ArrayUsePipelineShops' => [],
            'ArrayUsePipelineNumbers' => [],
            'ArrayUsePriority' => [],
        );

        file_put_contents(__DIR__ . '/../../../db/dbAdditionally.json', json_encode($this->DB));
    }

    public function __destruct()
    {
        file_put_contents(__DIR__ . '/../../../db/dbAdditionally.json', json_encode($this->DB));
    }


}