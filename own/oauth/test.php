<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require_once __DIR__ . '/modules/OAuth.php';

$account_id = 654156;


//смотрим токен есть ли он
$oauth = new OAuth();
$oauth->createToken();
$oauth->Token()->getAccess();
//есть ли токен
//если да, то проверем его время жизни если валидный отдаем аКСЕСС ТОКЕН.


