<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../modules/OAuth.php';
require_once __DIR__ . '/../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/../modules/users/Users.php';
require_once __DIR__ . '/../modules/users/CreateUserStruct.php';
require_once __DIR__ . '/../modules/users/ValidationPhone.php';
require_once __DIR__ . '/../../../integrations/store/core_crm/Factory.php';
require_once __DIR__ . '/../../../lib/generals/CONSTANTS.php';

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
////
//$_REQUEST = [
//    'flag' => 'check_user',
//    'user_id' => '6200425',
//    'referrer' => 'pragmadev.amocrm.ru',
//    'account_id' => '28967662',
//    'integration_id' => '022c8894-123d-47c3-8033-f8c95137bd5d',
//];


if (isset($_REQUEST['integration_id'])) {


    $REQUEST = $_REQUEST;
    $referrer = $REQUEST['referrer'];
    $integration_id = $REQUEST['integration_id'];
    $phone = $REQUEST['phone'];
    $flag = $REQUEST['flag'];
    $name_user = $REQUEST['name_user'] ?? '';

    $logger = new LogJSON($referrer, 'Message');
    $logger->set_container('');
    $subdomain = explode('.', $referrer)[0];

    try {
        $logger->add('REQUEST', $_REQUEST);
        \oAuth\Factory::init($subdomain, $integration_id, $logger);
        switch ($flag) {
            case 'create_code':
                $user = \oAuth\Users::getUser($name_user, $phone);
				if(!\Configs\Configs::isDev())
                	\oAuth\ValidationPhone::sendCode($phone);
                break;

            case 'check_user':
                $ami_id = $REQUEST['user_id'];
                $integration = \oAuth\Factory::getIntegration();
                $answer = $integration->check_user($ami_id);
                $user = findUser();
                if ($user) {
                    $token = \oAuth\Factory::getNode()->createInactiveApiKey($user->getPragmaUserId());
                    echo json_encode($token);
                } else
                    throw new \Exception('User not found', 202);
                break;

            case 'check_code':
                $code = $REQUEST['code'];
                $ami_id = $REQUEST['user_id'];
                $email = $REQUEST['email'];
                $userData = \oAuth\Factory::getIntegration()->getUserStruct($phone);
                $pragma_id_user = $userData->getId();
                $user = getUser($phone);
				if(!\Configs\Configs::isDev())
                	\oAuth\ValidationPhone::validCode($phone, $code);
                \oAuth\Factory::getIntegration()->addUser($user->getPragmaUserId(), $email, $pragma_id_user);
                $token = \oAuth\Factory::getNode()->createInactiveApiKey($user->getPragmaUserId());
                echo json_encode($token);
                break;
        }
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(['statusCode' => $e->getCode()]);
        $logger->send_error($e);
    }
}

function getUser($phone): \Services\General\iUser
{
    $user = \Services\Factory::getUsersService()->findByPhone($phone);
    if (!$user) throw new \Exception("User not found '$phone'");
    return $user;
}



function findUser(): \Services\General\iUser|null {
    if(\oAuth\Factory::getNode()?->getUser())
        return \oAuth\Factory::getNode()?->getUser();

    $ami_id = $_REQUEST['user_id'];
    $integration = \oAuth\Factory::getIntegration();
    $phone = $integration->getPhoneOnAmoId($ami_id) ?? null;
    $user = $phone ? \Services\Factory::getUsersService()->findByPhone($phone) : null;

    return $user ? $user : null;
}