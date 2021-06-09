<?php

namespace Autocall\Pragma;

//extends \Generals\CRMDB

class LiraxSettingsSchema
{
    private $DB;
    private int $id;


    public function __construct(int $pragma_account_id)
    {

        $json = file_get_contents(__DIR__ . '/../../../db/db.json');
        $this->DB = json_decode($json, true);
        $this->id = $pragma_account_id;
        $this->init();
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
            'pipelines' => array(),
            'settings' => array(
                'id_account' => '1',
                'use_store' => 'false',
                'use_priory' => 'false',
                'use_number' => 'false',
                'use_responsible' => '1',
                'token' => ' ',
                'refer' => '1',
                'application' => '1',
            )
        );

        file_put_contents(__DIR__ . '/../../../db/db.json', json_encode($this->DB));
    }


    function getGeneralSettingsModel(): array
    {
        return $this->DB[$this->id]['settings'];

    }


    function setToken(string $newToken)
    {
        $this->DB[$this->id]['settings']['token'] = $newToken;
    }


    public function setUseStore(string $use)
    {
        $this->DB[$this->id]['settings']['use_store'] = $use;

    }

    public function setUseNumber(string $use)
    {
        $this->DB[$this->id]['settings']['use_number'] = $use;

    }

    public function setUsePriory(string $use)
    {
        $this->DB[$this->id]['settings']['use_priory'] = $use;

    }

    public function setUseResponsible(string $quantity)
    {
        $this->DB[$this->id]['settings']['use_responsible'] = $quantity;

    }

    public function setReferrer(string $referrer)
    {
        $this->DB[$this->id]['settings']['refer'] = $referrer;

    }

    public function setApplication(int $id)
    {
        $this->DB[$this->id]['settings']['application'] = $id;

    }

    public function __destruct()
    {
        file_put_contents(__DIR__ . '/../../../db/db.json', json_encode($this->DB));
    }
}