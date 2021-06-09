<?php
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/temp/core.php';
require_once __DIR__ . '/temp/activity/act.php';
require_once __DIR__ . '/temp/Lirax/LogLirax.php';

$logger = new LogJSON()

if ($_POST) {
    $POST = $_POST;
    $account_id = intval($POST['account_id']);
    $referrer = HOOK::return_referrer($account_id);
    LogLirax::create_log($referrer, 'HOOK');

    $HOOK = new HOOK($account_id, $referrer);
    $HOOK->POST($POST);

}

if ($_GET) {
    $GET = $_GET;
    $account_id = $GET['id_account'];
    $referrer = HOOK::return_referrer($account_id);
    LogLirax::create_log($referrer, 'HOOK');

    $HOOK = new HOOK($account_id, $referrer);
    $HOOK->GET($GET);
}

class HOOK
{
    private string $referrer;
    private int $account_id;

    public function __construct(int $account_id, string $referrer)
    {
        $this->account_id = $account_id;
        $this->referrer = $referrer;
    }

    public function GET($GET){

        if ($this->referrer) {

            $id_hook = $GET['id_hook'];
            $core = new core($this->account_id);

            switch ($id_hook) {

                case 'missed':

                    LogLirax::LOG('Missed_Hooks', $GET);
                    $phone = $GET['ani'] * 1;
                    $id_pip = $core->get_id_pip($phone);
                    LogLirax::LOG('$id_pip', $id_pip);

                    $res = act::include($this->account_id, $id_pip);
                    if ($res) {
                        LogLirax::LOG('YESS_OPLATA', '');
                        $core->call_missed($phone);
                    } else {
                        LogLirax::LOG('NOO_OPLATA', '');
                    }
                    break;
                case 'adopted':
                    LogLirax::LOG('Adopted_Hooks', $GET);
                    $phone = $GET['ani'] * 1;
                    $dnis = $GET['dnis'];


                    $id_pip = $core->get_id_pip($phone);
                    LogLirax::LOG('$id_pip', $id_pip);
                    $answer = act::include($this->account_id, $id_pip);

                    if ($answer) {
                        LogLirax::LOG('YESS_OPLATA', '');

                        $res = strlen($dnis) > 4;
                        switch ($res) {
                            case true:
                                $core->call_accepted($phone);
                                break;
                            default:
                                break;
                        }
                    } else {
                        LogLirax::LOG('NOO_OPLATA', '');
                    }
                    break;
                case 'outgoing':
                    LogLirax::LOG('Outgoing_Hooks', $GET);
                    $phone = HOOK::search_number($GET);
                    $id_pip = $core->get_id_pip($phone);
                    $answer = act::include($this->account_id, $id_pip);
                    if ($answer) {
                        $core->call_outgoing($GET);
                        LogLirax::LOG('YESS_OPLATA', '');

                    } else {
                        LogLirax::LOG('NOO_OPLATA', '');

                    }
                    break;

            }
        }
    }


    public function POST($POST)
    {

        if ($this->referrer) {
            LogLirax::create_log($this->referrer, 'Core');

            $id_leads = intval($POST['event']['data']['id']);
            $id_pipelines = intval($POST['event']['data']['pipeline_id']);
            $res = act::include($this->account_id, $id_pipelines);
            LogLirax::LOG('HOOK', ['account_id' => $this->account_id, "id_leads" => $id_leads, "id_pipelines" => $id_pipelines]);

            switch ($res) {
                case false:
                    LogLirax::LOG('NOO_OPLATA', 'NOO_OPLATA NOO_OPLAT');
                    break;
                default:
                    LogLirax::LOG('YESS_OPLATA', 'YESS_OPLATA');
                    $core = new core($this->account_id);
                    $CALL = $core->RUN($id_leads, $id_pipelines);
                    LogLirax::LOG('CALL', $CALL);
                    break;
            }
        } else {
            LogLirax::LOG('No', "Client");
        }

    }

    static function check_payment()
    {

    }

    static function return_referrer(int $account_id): string
    {
        return LogLirax::return__referrer($account_id);
    }

    static function search_number($arr)
    {
        $dnis = $arr['dnis'];
        if (iconv_strlen($dnis) > 8) {
            return $dnis;
        } else {
            return $arr['dest'];
        }
    }
}


