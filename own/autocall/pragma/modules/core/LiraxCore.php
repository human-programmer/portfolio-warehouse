<?php

namespace Autocall\Pragma;

use DateTime;
use DateTimeZone;

require_once __DIR__ . '/../../business_rules/core/iLiraxCore.php';
require_once __DIR__ . '/LiraxCoreSchema.php';
require_once __DIR__ . '/LiraxCoreStorage.php';

class LiraxCore implements iLiraxCore
{



    protected LiraxCoreSchema $LiraxCoreSchema;
    /**
     * @var LiraxCoreStorage
     */
    private LiraxCoreStorage $LiraxCoreStorage;




    public function __construct(int $pragma_account_id, int $Phone)
    {
        $this->LiraxCoreSchema = new LiraxCoreSchema($pragma_account_id, $Phone);
        $this->LiraxCoreStorage = new LiraxCoreStorage($pragma_account_id, $Phone);

    }


     function getLiraxCoreStorage(): iLiraxCoreStorage
    {
        return $this->LiraxCoreStorage;
    }



    function getPhoneStruct(): iLiraxCoreStruct
    {
        $LiraxCoreStructModel = $this->LiraxCoreSchema->getModelPhone();
        return new LiraxCoreStruct(
            $LiraxCoreStructModel['status'],
            $LiraxCoreStructModel['mode'],
        );
    }





    function setQuantity(): void
    {
        // TODO: Implement setQuantity() method.
    }

    function setStatus(int $status): void
    {
        $this->LiraxCoreSchema->setStatus($status);
    }

    function getStatus(): int
    {
        return $this->LiraxCoreSchema->getStatus();
    }

    function setMode(bool $mode): void
    {
        $this->LiraxCoreSchema->setMode($mode);
    }

    function getMode(): bool
    {
        return $this->LiraxCoreSchema->getMode();
    }

    function getWorkTime(): int
    {
        $data = Factory::getLiraxAdditionallySettings()->getSettingsStruct()->getNumber_of_call_attempts();
        $res = explode(":", $data['work_start']);
        $ref = explode(":", $data['work_finish']);
        Factory::getLogWriter()->add('$res', $res);

        $start = $res[0] * 1;
        $finish = $ref[0] * 1;

        Factory::getLogWriter()->add('$start', $start);
        Factory::getLogWriter()->add('$finish', $finish);

        return self::hour_timer($finish, $start);

    }
    private function hour_timer(int $begin_hour, int $end_hour): int
    {
        // Доработать с минутами

        $time = $this->now_time();
        Factory::getLogWriter()->add('$time', $time);

        $hours = $time['hour'];
//        $min = $time['min'];
//        $hours = 0;


        switch ($hours) {
            case 0:
                return $end_hour;
            case  $hours >= $begin_hour && $hours < 24 :
                return 24 - $hours + $end_hour;
            case $hours < $end_hour:
                return $end_hour - $hours;
            default:
                return -1;
        }

//        return match ($hours) {
//            0 => $end_hour,
//            $hours >= $begin_hour && $hours < 24 => 24 - $hours + $end_hour,
//            $hours < $end_hour => $end_hour - $hours,
//            default => -1
//        };
    }

    private function now_time(): array
    {
        $dateTime = new DateTime();
        $dateTime->setTimeZone(new DateTimeZone('EUROPE/Moscow'));
        $hour = intval($dateTime->format('H'));
        $min = intval($dateTime->format('i'));
        return ['hour' => $hour, 'min' => $min];
    }

}