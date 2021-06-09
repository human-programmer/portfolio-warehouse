<?php

//namespace PragmaStorage;
//require_once __DIR__ . '/../../../lib/log/LogJSON.php';
//require_once __DIR__ . '/lib/Factory.php';
//
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//$log_writer = new \LogJSON('salesdermcareru.amocrm.ru', 'PragmaStorage', 'test');
//
//Factory::initById('PragmaStorage', 29186176, $log_writer);
//$node = \Services\Factory::getNodesService()->findAmocrmNodeCode('PragmaStorage',"salesdermcareru");
//$node->setShutdownTime(1622195091);

$arr = ["MIN(`pragma_storage`.`stores`.`id`)" => 583];

echo current($arr);