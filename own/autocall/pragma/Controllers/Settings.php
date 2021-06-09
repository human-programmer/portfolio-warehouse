<?php


namespace Autocall;

require_once __DIR__ . '/../Factory.php';


class Set
{
    private int|string $account_id;
    private string $referrer;
    private string $flag;
    private string $typeCRM;
    private array $REQUEST;

    public function __construct($REQUEST)
    {

        $this->typeCRM = $REQUEST['typeCRM'];

        if (isset($this->typeCRM)) {
            switch ($this->typeCRM) {
                case 'amocrm':
                    $this->AMOCRM_INIT($REQUEST);
                    break;

                case 'bitrix':
                    $this->BITRIX_INIT($REQUEST);
                    break;
            }
        }


    }

    private function BITRIX_INIT(array $REQUEST): void
    {
        $this->account_id = $REQUEST['ID_ACCOUNT'];
        Bitrix\Factory::init($this->account_id);




    }


    private function AMOCRM_INIT(array $REQUEST): void
    {
        $this->account_id = intval($REQUEST['ID_ACCOUNT']);
        Amocrm\Factory::init($this->account_id);
        $this->REQUEST = $REQUEST;
        $this->referrer = Amocrm\Factory::getAccountsModule()->getAmoReferer();
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

    private    function save_settings()
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


        Amocrm\Factory::getLiraxSettings()->saveToken(trim($token));
        Amocrm\Factory::getLiraxSettings()->saveReferrer($this->referrer);
        Amocrm\Factory::getLiraxSettings()->saveApplication($APPLICATION);

        Amocrm\Factory::getLiraxSettings()->saveUseNumber($use_number);
        Amocrm\Factory::getLiraxSettings()->saveUseStore($use_store);
        Amocrm\Factory::getLiraxSettings()->saveUsePriory($use_priory);
        Amocrm\Factory::getLiraxSettings()->saveUseResponsible($use_responsible);

        Amocrm\Factory::getLiraxAdditionallySettings()->saveNumberOfCallAttempts(self::DECODE_($QUANTITY));
        Amocrm\Factory::getLiraxAdditionallySettings()->saveArrayUsePipelineShops(self::DECODE_($ARRAY_PIPELINE));
        Amocrm\Factory::getLiraxAdditionallySettings()->saveArrayUsePipelineNumbers(self::DECODE_($ARRAY_NUM_PIP));
        Amocrm\Factory::getLiraxAdditionallySettings()->saveArrayUsePriority(self::DECODE_($ARRAY_PRIORY));

    }

    #[NoReturn] private function get_settings(): void
    {


        $data = Amocrm\Factory::getLiraxSettings()->getSettingsStruct();
        $DATA = $data->getArrayToSave();
        $DATA['refer'] = Amocrm\Factory::getAccountsModule()->getAmoReferer();
        $DATA['id_account'] = Amocrm\Factory::getAccountsModule()->getAmoAccountId();

        $LiraxAdditionallySettings = Amocrm\Factory::getLiraxAdditionallySettings()->getSettingsStruct();


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
        $ARR = json_encode($array, JSON_UNESCAPED_UNICODE);
        return json_decode($ARR, true);
    }

}