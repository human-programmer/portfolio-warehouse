<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/db/SettingsDB.php';

$POST = $_POST;


if ($POST) {
    $account_id = $POST['account_id'];
    $settings = new SettingsDB($account_id);
    $flag = $POST['flag'];
    switch ($flag) {
        case 'save':
            $data = array(
                "input_key" => $POST['input_key'],
                "input_login" => $POST['input_login']
            );
            $settings->save($data);

            break;

        case 'get':
            $data = $settings->get();
            echo json_encode($data);

    }


}




