<?php
namespace Autocall\Bitrix;

interface iBitrixSettings extends \Autocall\Pragma\iLiraxSettings
{
    function setPipelineId($id): void;
}