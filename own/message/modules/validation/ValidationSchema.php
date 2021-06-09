<?php

namespace market;


class ValidationSchema
{
    /**
     * @var mixed
     */

    private string $path = __DIR__ . '/../../db/db.json';

    private $DB;

    public function __construct(private int $user_id)
    {
        $json = file_get_contents($this->path);
        $this->DB = json_decode($json, true);

    }

    public function addCode(int $code)
    {
        $this->DB[$this->user_id] = $code;
        file_put_contents($this->path, json_encode($this->DB));
    }

    public function checkCode(): int
    {
        return $this->DB[$this->user_id];
    }

    public function deleteCode()
    {
        unset($this->DB[$this->user_id]);
    }

    public function __destruct()
    {
        file_put_contents($this->path, json_encode($this->DB));
    }

}