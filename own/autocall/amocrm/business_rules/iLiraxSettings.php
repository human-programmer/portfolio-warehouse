<?php


namespace Autocall\Amocrm;


interface iLiraxSettings extends \Autocall\Pragma\iLiraxSettings
{
    function setPipelineId(int $pipeline_id): void;

}