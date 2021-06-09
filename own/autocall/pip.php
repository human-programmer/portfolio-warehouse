<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/lib/portals/AmoCRM/Lirax/LogLirax.php';
require_once __DIR__ . '/lib/portals/AmoCRM/activity/act.php';


if ($_POST) {
    $POST = $_POST;
    if (isset($POST['flag'])) {
        $flag = $POST['flag'];
        $account_id = intval($POST['account_id']);
        $referrer = LogLirax::return__referrer($account_id);
        LogLirax::create_log($referrer, 'Pip');
        switch ($flag) {
            case 'save':
                $ACCOUNT_ID = intval($POST['account_id']);
                $ID = intval($POST['id']);
                $REFERRER = $POST['referrer'];
                $CHECK = $POST['CHECK'];

                switch ($CHECK) {
                    case 'true':
                        (new act())->save_pipe($ACCOUNT_ID, $ID);
                        break;
                    case 'false':
                        (new act())->delete_pipe($ACCOUNT_ID, $ID);
                        break;
                }
                break;
            case 'get':
                $ACCOUNT_ID = intval($POST['account_id']);
                $data = (new act())->get_settings($ACCOUNT_ID);
                echo json_encode($data);
                break;
        }
    }
}

