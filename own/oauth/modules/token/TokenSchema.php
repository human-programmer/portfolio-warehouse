<?php


require_once __DIR__ . '/../../../../../api/lib/generals/functions/FileHandler.php';

use Generals\Functions\FileHandler;

class TokenSchema
{
    private string $account_id;
    private string $path = __DIR__ . '/../../../../../temp/OAuth/db.json';
    private $DB;

    public function __construct(string $account_id)
    {
        $this->account_id = $account_id;
        if (!FileHandler::check('OAuth', 'db')) {
            FileHandler::set('OAuth', 'db', []);
        }
        $json = file_get_contents($this->path);
        $this->DB = json_decode($json, true);
    }

    function createToken(string $token): void
    {
        $this->DB[$this->account_id] = $token;
    }

    function getAccessToken(): string
    {
        $token = $this->DB[$this->account_id];
        $pieces = explode("A", $token);
        return $pieces[0];
    }

    function getLiveToken(): int
    {
        $token = $this->DB[$this->account_id];
        preg_match("/A.*R/", $token, $keywords);
        $timeLive = substr($keywords[0], 1,-1);
        return $timeLive - time();
    }


    function getRefreshToken(): string
    {
        $token = $this->DB[$this->account_id];
        $pieces = explode("R", $token);
        return $pieces[1];
    }


    function isExist():bool
    {
        return empty($this->DB[$this->account_id]);
    }


    public function __destruct()
    {
        file_put_contents($this->path, json_encode($this->DB));
    }


}