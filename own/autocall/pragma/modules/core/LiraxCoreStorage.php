<?php


namespace Autocall\Pragma;

use JetBrains\PhpStorm\Pure;

require_once __DIR__ . '/../../business_rules/core/iLiraxCoreStorage.php';


class LiraxCoreStorage implements iLiraxCoreStorage
{
    public function __construct(
        private int $AccountId,
        private int $Phone,
    )
    {
    }

    #[Pure] function getExistGeneralFile(): bool
    {
        $path = __DIR__ . "/library/$this->AccountId/$this->Phone.php";
        return file_exists($path);
    }


    function initFile(string $filename, string $data, int $time): void
    {
        $F_PATH = "/library/$this->AccountId/$filename" . "_" . "$this->Phone.php";
        $path = __DIR__ . $F_PATH;
        self::create_folders($path);
        self::create($path, $data, $time);
        Factory::getLogWriter()->add('data', $data);
        self::launch($F_PATH);
    }


    function initFileUNLINK(string $filename, string $data, int $time): void
    {
        $F_PATH = "/library/$this->AccountId/$filename" . "_" . "$this->Phone.php";
        $path = __DIR__ . $F_PATH;
        self::create_folders($path);
        self::createUNLINK($path, $data, $time);
        Factory::getLogWriter()->add('data', $data);
        self::launch($F_PATH);
    }

    function create_folders(string $path)
    {

        $_path = dirname($path);
        if (!file_exists($_path)) {
            mkdir($_path, 0755, true);
        }
    }

    function create($path, $data, $time)
    {
        Factory::getLogWriter()->add('$path', $path);

        file_put_contents($path, "<?php
sleep($time);
$data
unlink(__FILE__);
");
    }

    function createUNLINK($path, $data, $time)
    {
        Factory::getLogWriter()->add('$path', $path);

        file_put_contents($path, "<?php
sleep($time);
$data
");
    }

    function launch(string $filename): string
    {
        Factory::Log('str', "https://smart.core_crm.by/api/own/autocall/core_crm/modules/core$filename");
        return exec('curl  https://smart.core_crm.by/api/own/autocall/core_crm/modules/core' . $filename . ' > /dev/null &');
    }

}