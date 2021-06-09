<?php
header('Access-Control-Allow-Origin: *');


$str = "{'subdomain': 'pragmadev', 'key': 'uyguiguigi'}";
$stra = json_encode($str);
$str1 = json_decode(str, true);
echo json_encode($str1);