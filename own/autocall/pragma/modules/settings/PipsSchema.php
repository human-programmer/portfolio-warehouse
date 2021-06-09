<?php


namespace Autocall\Pragma;


class PipsSchema
{

    private string $path = __DIR__ . '/../../../db/pip.json';
    private mixed $db;

    public function __construct(protected int $account_id)
    {
        $json = file_get_contents($this->path);
        $this->db = json_decode($json, true);

    }


    function add_account()
    {
        $this->db[$this->account_id] = array(
            'pipelines' => array(),
            'quantity' => 5,
            'activity' => false
        );
        $this->save();
    }

    function search_key(): bool
    {
        return array_key_exists($this->account_id, $this->db);
    }

    function return_pip()
    {
        return $this->db[$this->account_id]['pipelines'];
    }

    function save_new_arr(array $arr)
    {
        $this->db[$this->account_id]['pipelines'] = $arr;
    }

    function getQuantity()
    {
        return $this->db[$this->account_id]['quantity'];
    }

    function save()
    {
        file_put_contents($this->path, json_encode($this->db));
    }

    function get(): array|null
    {
        return $this->db[$this->account_id];
    }




}