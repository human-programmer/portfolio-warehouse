<?php


namespace oAuth;

use Generals\Functions\FileHandler;
use JetBrains\PhpStorm\Pure;
use market\smsP;
use Services\General\iUser;

require_once __DIR__ . '/../../../../lib/generals/functions/FileHandler.php';
require_once __DIR__ . '/../../../message/modules/smsp/smsP.php';

class ValidationPhone
{
    private static string $dir = "ValidationPhone";

    static function validCode(int $phone, int $enterCode): void
    {
        $code = self::getCurrentCode($phone);

        if (!$code || $code != $enterCode)
            throw new \Exception('Invalid code', 665);

        self::delCode($phone);
    }


    private static function getCurrentCode(int $phone): int|null
    {
        $data = self::get();
        return intval($data[$phone]['code']);
    }

    static private function get(): array
    {
        return FileHandler::get(self::$dir, 'code');
    }

    static function sendCode(int $phone): void
    {
        $code = self::generateCode($phone);
//        self::sendMessage($phone, $code);
    }

    private static function generateCode(int $phone): string
    {
        $code = self::createCode();
        $arr = self::get();
        $arr[$phone] = ['code' => $code];
        self::save($arr);
        return $code;
    }

    private static function delCode(int $phone): void
    {
        $arr = self::get();
        unset($arr[$phone]);
        self::save($arr);

    }

    static private function save(array $content)
    {
        FileHandler::set(self::$dir, 'code', $content);
    }

    private static function sendMessage(int $phone, string $code)
    {
        if (!$phone)
            throw new \Exception();
        $smsP = new smsP($phone);
        $smsP->sendMessage($code);
    }

    #[Pure] private static function createCode(): int
    {
        return mt_rand(1001, 9999);
    }

    private static function deleteCode()
    {

    }

}
