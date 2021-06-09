<?php
namespace Autocall\Amocrm;

require_once __DIR__ . '/DurationEqualsZero.php';
require_once __DIR__ . '/DurationMoreZero.php';


class Duration extends Outgoing
{

    static function DurationMoreZero(int $phone){
        $lengthPhone = strlen($phone) > 5;
        switch ($lengthPhone) {
            case true:
                DurationMoreZero::DurationMoreZeroDenisMoreFive($phone);
                break;
            case false:
                Factory::log('Duration Less 5 length_denis < 5', "__END__ =)");
                break;
        }

    }


    static function DurationEqualsZero(string $denis, array $REQUEST){
        $length_denis = strlen($denis) > 5;
        switch ($length_denis) {
            case true:
                $phone = $REQUEST['dnis'] * 1;
                Factory::log("DurationEqualsZeroDenisMoreFive", $phone);
                DurationEqualsZero::DurationEqualsZeroDenisMoreFive($phone);     // > 5
                break;
            case false:
                $phone = $REQUEST['dest'] * 1;
                Factory::log("DurationEqualsZeroDenisLessFive", $phone);
                DurationEqualsZero::DurationEqualsZeroDenisLessFive($phone);     // < 5
                break;
        }
    }


}