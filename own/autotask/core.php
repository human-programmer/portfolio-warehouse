<?php

header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/LogTask.php';
LogTask::create_log("AutoTask", "hook");


$POST = $_POST;
$array = json_encode($POST);
$str = "'$array'";
LogTask::LOG('$str', $str);
$output = exec("python3.9 /var/www/core_crm/data/projects/python_project/AutoTask/core.py $str");



