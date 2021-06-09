<?php
require_once __DIR__ . '/../../amocrm/Factory.php';


use Autocall\Amocrm\Factory;
use JetBrains\PhpStorm\NoReturn;

class AutoCallSettings
{
    private int $account_id;
    private string $referrer;
    private string $flag;
    private array $REQUEST;

    public function __construct($REQUEST)
    {
        $this->account_id = intval($REQUEST['ID_ACCOUNT']);

        $typeCRM = $REQUEST['typeCRM'];

        switch ($typeCRM){
            case 'amocrm':
                $this->amocrm($REQUEST);
            break;

            case 'bitrix':
                $this->bitrix($REQUEST);

        }



    }

    private function bitrix(array $REQUEST) {

    }



    private function amocrm(array $REQUEST)
    {

        Factory::init($this->account_id);
        $this->REQUEST = $REQUEST;
        $this->referrer = Factory::getAccountsModule()->getAccount()->getAmocrmReferer();
        $this->flag = $REQUEST['FLAG'];
        switch ($this->flag) {
            case "save_settings":
                $this->save_settings();
                break;
            case "get_settings":
                $this->get_settings();
                break;
        }
    }



    private function save_settings()
    {
        $REQUEST = $this->REQUEST;
        $token = strval($REQUEST['TOKEN']);
        $QUANTITY = $REQUEST['QUANTITY'];
        $APPLICATION = intval($REQUEST['APPLICATION']);
        $use_store = strval($REQUEST['USE_STORE']);
        $use_number = strval($REQUEST['USE_NUMBER']);
        $use_responsible = strval($REQUEST['USE_RESPONSIBLE']);
        $use_priory = strval($REQUEST['USE_PRIORY']);
        $ARRAY_PIPELINE = $REQUEST['ARRAY_PIPELINE'];
        $ARRAY_NUM_PIP = $REQUEST['ARRAY_NUM_PIP'];
        $ARRAY_PRIORY = $REQUEST['ARRAY_PRIORY'];


        Factory::getLiraxSettings()->saveToken(trim($token));
        Factory::getLiraxSettings()->saveReferrer($this->referrer);
        Factory::getLiraxSettings()->saveApplication($APPLICATION);

        Factory::getLiraxSettings()->saveUseNumber($use_number);
        Factory::getLiraxSettings()->saveUseStore($use_store);
        Factory::getLiraxSettings()->saveUsePriory($use_priory);
        Factory::getLiraxSettings()->saveUseResponsible($use_responsible);

        Factory::getLiraxAdditionallySettings()->saveNumberOfCallAttempts(self::DECODE_($QUANTITY));
        Factory::getLiraxAdditionallySettings()->saveArrayUsePipelineShops(self::DECODE_($ARRAY_PIPELINE));
        Factory::getLiraxAdditionallySettings()->saveArrayUsePipelineNumbers(self::DECODE_($ARRAY_NUM_PIP));
        Factory::getLiraxAdditionallySettings()->saveArrayUsePriority(self::DECODE_($ARRAY_PRIORY));

    }

    #[NoReturn] private function get_settings(): void
    {


        $data = Factory::getLiraxSettings()->getSettingsStruct();
        $DATA = $data->getArrayToSave();
        $DATA['refer'] = Factory::getAccountsModule()->getAccount()->getAmocrmReferer();
        $DATA['id_account'] = Factory::getAccountsModule()->getAccount()->getAmocrmAccountId();

        $LiraxAdditionallySettings = Factory::getLiraxAdditionallySettings()->getSettingsStruct();


        $NUMBERS = $LiraxAdditionallySettings->getArrayUsePipelineNumbers();
        $PIPELINES = $LiraxAdditionallySettings->getArrayUsePipelineShops();
        $QUANTITY = $LiraxAdditionallySettings->getNumber_of_call_attempts();
        $PRIORY = $LiraxAdditionallySettings->getArrayUsePriority();

        $Settings = array(
            'data' => [$DATA],
            'numbers' => $NUMBERS,
            'pipelines' => $PIPELINES,
            'quantity' => $QUANTITY,
            'priority' => $PRIORY,
        );
        echo json_encode($Settings);
        die();
    }


    static function DECODE_($array)
    {
        $ARR = json_encode($array,JSON_UNESCAPED_UNICODE);
        return json_decode($ARR, true);
    }


}