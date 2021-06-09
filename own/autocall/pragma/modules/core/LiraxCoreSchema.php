<?php


namespace Autocall\Pragma;


class LiraxCoreSchema
{
    private $DB;
    private int $id;
    private int $Phone;


    public function __construct(int $pragma_account_id, int $Phone)
    {

        $json = file_get_contents(__DIR__ . '/../../../db/dbPhone.json');
        $this->DB = json_decode($json, true);
        $this->id = $pragma_account_id;
        $this->Phone = $Phone;
        $this->init();
    }

    function getModelPhone(): array
    {
        return $this->DB[$this->id][$this->Phone];
    }

    function setStatus(int $status): void
    {
        $this->DB[$this->id][$this->Phone]['status'] = $status;
    }

    function getStatus(): int
    {
        return $this->DB[$this->id][$this->Phone]['status'];
    }


    function setMode(bool $mode): void
    {
        $this->DB[$this->id][$this->Phone]['mode'] = $mode;
    }

    function getMode(): bool
    {
        return $this->DB[$this->id][$this->Phone]['mode'];
    }


    private function init()
    {
        if (!empty($this->DB[$this->id])) {
            $keys = array_keys($this->DB[$this->id]);
            switch (in_array($this->Phone, $keys)) {
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
        $this->DB[$this->id][$this->Phone] = array(
            'status' => 0,
            'mode' => 0
        );
        file_put_contents(__DIR__ . '/../../../db/dbPhone.json', json_encode($this->DB));
    }

    public function __destruct()
    {
        file_put_contents(__DIR__ . '/../../../db/dbPhone.json', json_encode($this->DB));
    }
}