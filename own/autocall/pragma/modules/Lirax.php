<?php

namespace Autocall\Pragma;
require_once __DIR__ . '/../business_rules/iLirax.php';

use Autocall\Amocrm\Factory;
use Autocall\Amocrm\iLiraxSettings;
use RingCentral\Tests\Psr7\Str;


class Lirax implements iLirax
{
    //api lirax

    public function __construct(private iLiraxSettingsStruct $settings_struct)
    {
    }

    function call(): void
    {
        $str = $this->settings_struct->getHttpQuery();
        echo json_encode($this->_REQUEST($str));

    }

    function IsFreeUsers(string $responsible, string $amo): array
    {
        $token = $this->settings_struct->getToken();
        $str = "cmd=IsFreeUsers&token=$token&phones=$responsible&amo=$amo";
        return $this->_REQUEST($str);
    }


    function getUserSips(): string
    {
        $ARRAY = [];
        $STR = "";
        $answer = $this->requestUserSIPs();
        if ($answer) {
            $array = $answer['user_sips'];
            foreach ($array as $item) {
                array_push($ARRAY, $item['ext']);
            }
            $UNIQ_ARR = array_unique($ARRAY);
            foreach ($UNIQ_ARR as $item) {
                $STR .= $item . ',';
            }
        }
        return substr($STR, 0, -1);
    }


    function requestUserSIPs(): array
    {
        $token = $this->settings_struct->getToken();
        $STR = "token=$token&cmd=getUserSips&ext=5000";
        return $this->_REQUEST($STR);
    }

    function _REQUEST(string $str)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://by.lirax.net:8482/general',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $str,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        Factory::getLogWriter()->add('$str', $str);

        $response = curl_exec($curl);
        curl_close($curl);
        $JSON = json_decode($response, true);
        Factory::getLogWriter()->add('_REQUEST', $JSON);
        return $JSON;
    }


    function getLiraxSettingsStruct(): iLiraxSettingsStruct
    {
        return $this->settings_struct;
    }


}