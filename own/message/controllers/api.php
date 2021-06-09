<?php

namespace market;

use LOGMESSAGE;

header('Access-Control-Allow-Origin: *');

$REQUEST = $_REQUEST;
$account_id = $REQUEST['account_id'];
$referrer = $REQUEST['referrer'];
require_once __DIR__ . '/../LOGMESSAGE.php';
require_once __DIR__ . '/../../../integrations/store/core_crm/Factory.php';

LOGMESSAGE::create_log($referrer, 'Message');

require_once __DIR__ . '/../modules/validation/Validation.php';
require_once __DIR__ . '/../modules/smsp/smsP.php';

$phone = intval($REQUEST['phone']);
$flag = $REQUEST['flag'];
Factory::init($account_id);

switch ($flag) {
    case 'create_code':
        $valid = new Validation($phone);
        $valid->createCode();
        $code = $valid->getCode();
//        $smsP = new smsP($phone);
//        $smsP->sendMessage($code);
        LOGMESSAGE::LOG('create_code', [$phone => $code]);
        break;

    case 'check_code':
        $valid = new Validation($phone);
        $code = intval($REQUEST['code']);
        $amocrm_id = intval($REQUEST['amocrm_id']);
        $email = $REQUEST['email'];
        $name_user = $REQUEST['name_user'];
        LOGMESSAGE::LOG('check_code', [$phone => $code]);
        $check = $valid->checkCode($code);
        echo json_encode($check);

        if ($check) {
            $valid->deleteCode();
            $valid->addPhoneDB($email);
            $pragma_id  = Factory::getIntegration()->addUser($phone, $name_user);


        }
        break;
}

