<?php


require_once __DIR__ . '/../../../../api/lib/generals/functions/FileHandler.php';

use Generals\Functions\FileHandler;

class OAuthSchema
{
    private string $account_id;
    private string $path = __DIR__ . '/../../../../temp/OAuth/db.json';
    private $DB;

    public function __construct(string $account_id)
    {
        $this->account_id = $account_id;
        FileHandler::set('OAuth', 'db.json', []);
        $json = file_get_contents($this->path);
        $this->DB = json_decode($json, true);
    }

    function createToken(string $token): void
    {
        $this->DB[$this->account_id]['token'] = $token;
    }

    function getAccessToken(): string
    {
        $token = $this->DB[$this->account_id]['token'];
        $pieces = explode(".", $token);
        return $pieces[0];
    }

    function getRefreshToken(): string
    {
        $token = $this->DB[$this->account_id]['token'];
        $pieces = explode(".", $token);
        return $pieces[1];
    }
    function getLiveToken(): int
    {
        $token = $this->DB[$this->account_id]['token'];
        $pieces = explode(".", $token);
        return intval($pieces[2]);
    }


    public function __destruct()
    {
        file_put_contents($this->path, json_encode($this->DB));
    }


}