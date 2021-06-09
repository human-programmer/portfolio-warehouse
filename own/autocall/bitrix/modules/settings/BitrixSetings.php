<?php

namespace Autocall\Bitrix;

require_once __DIR__ . '/../../business_rules/iBitrixSettings.php';


class BitrixSetings extends \Autocall\Pragma\LiraxSettings implements iBitrixSettings
{
    private $id = 1;

    function setPipelineId($id): void
    {
        $this->id = $id;
    }


}