<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../modules/OAuth.php';

$POST = $_POST;

if (isset($POST['account_id'])) {

    $oAuth = new OAuth($POST['account_id']);
    $answer = $oAuth->isExistsToken();
    switch ($answer) {
        case true:
            $tokenModel = $oAuth->Token();
            if ($tokenModel->getLive() > 3600) {
                echo json_encode($tokenModel->getAccess());
                die();
            } else {
                $oAuth->createToken();
                $tokenModel = $oAuth->Token();
                echo json_encode($tokenModel->getAccess());
                die();
            }
            break;
        case false:
            $oAuth->createToken();
            $tokenModel = $oAuth->Token();
            echo json_encode($tokenModel->getAccess());
            die();
            break;
    }
}
