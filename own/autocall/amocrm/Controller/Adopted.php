<?php

namespace Autocall\Amocrm;
require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/../../../../lib/log/LogJSON.php';
require_once __DIR__ . '/../../constants.php';

class Adopted
{
    private int $Phone;
    private string $referrer;

    public function __construct(array $REQUEST, $logger)
    {
        $this->referrer = $REQUEST['subdomain'];
        $this->Phone = $REQUEST['ani'] * 1;
        $dnis = $REQUEST['dnis'];
        if (strlen($dnis) > 4) {
            Factory::amocrmInit($this->referrer, $logger);
            $this->core();
        }

    }

    function core()
    {
        Factory::getLiraxCore($this->Phone)->getLiraxCoreStorage()->getExistGeneralFile() ? $this->TRUE() : $this->FALSE();
    }

    private function TRUE()
    {
        $MAX_QUANTITY = Factory::getLiraxAdditionallySettings()->getSettingsStruct()->getNumber_of_call_attempts()['quantity'];
        $new_status = $MAX_QUANTITY * 1;
        Factory::getLiraxCore($this->Phone)->setStatus($new_status);

    }

    private function FALSE()
    {
        Factory::getLiraxCore($this->Phone)->setStatus(0);
        Factory::getLiraxCore($this->Phone)->setMode(false);
    }


}