<?php


class SettingsDB
{
    private $db;
    private string $path = __DIR__ . '/db.json';

    public function __construct(private int $account_id)
    {
        $json = file_get_contents($this->path);
        $this->db = json_decode($json, true);
        $this->isExistAccount();
    }

    private function isExistAccount()
    {
        if(!empty($this->db)){
            $keys = array_keys($this->db);

            switch(in_array($this->account_id, $keys)){
                case true:
                    break;
                case false:
                    $this->db[$this->account_id] = array();
                    $this->_save();
                    break;
            }
        }

    }

    public function save(array $data)
    {
        $this->db[$this->account_id] = $data;
    }

    public function get()
    {
        return $this->db[$this->account_id];
    }

    private function _save()
    {
        file_put_contents($this->path, json_encode($this->db));
    }

    public function __destruct()
    {
        $this->_save();
    }


}