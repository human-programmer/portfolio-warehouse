<?php

namespace market;

use LOGMESSAGE;

require_once __DIR__ . '/../../business_rules/smsp/ismsP.php';
require_once __DIR__ . '/../../LOGMESSAGE.php';

class smsP implements ismsP
{
    private string $user = 'photobrand@gmail.com';
    private string $API_key = '3QPG8H6oeb';
    private string $message = ' - код для активации пароля в "PRAGMA"';
    private string $sender = 'PRAGMA';

    public function __construct(private int $phone)
    {
        LOGMESSAGE::create_log($phone , 'message');
    }

    function sendMessage(int $code): void
    {
        $this->message = $code . $this->message;

        $CURLOPT_POSTFIELDS = array(
            'user' => $this->user,
            'apikey' => $this->API_key,
            'recipients' => $this->phone,
            'message' => $this->message,
            'sender' => $this->sender
        );
        $this->REQUEST($CURLOPT_POSTFIELDS);
    }


    private function REQUEST(array $CURLOPT_POSTFIELDS)
    {
        $response = $this->response($CURLOPT_POSTFIELDS);
        LOGMESSAGE::LOG('res', $response);
    }

    function response($CURLOPT_POSTFIELDS)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://cp.smsp.by/?r=api/msg_send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $CURLOPT_POSTFIELDS,
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    function sendCustom(string $text): array
    {
        $CURLOPT_POSTFIELDS = array(
            'user' => $this->user,
            'apikey' => $this->API_key,
            'recipients' => $this->phone,
            'message' => $text,
            'sender' => $this->sender
        );
        return $this->response($CURLOPT_POSTFIELDS);
    }
}