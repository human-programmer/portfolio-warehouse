<?php
namespace market;


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__  . '/modules/validation/Validation.php';

$phone = 375292702250;
$email = 'vasyayurevich@gmail.com';

$Val = new Validation(375292702250);


$Val->deleteCode();
