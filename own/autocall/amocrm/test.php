<?php

namespace Autocall\Amocrm;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


$array = ['ani' => 375292702250, 'id_account' => 28967665];


$res = [
    [
        'q' => "1",
        'time' => "00:01",
        'id' => "quantity_1",
    ],
    [
        'q' => "2",
        'time' => "00:01",
        'id' => "quantity_2",
    ],
    [
        'q' => "3",
        'time' => "00:01",
        'id' => "quantity_3",
    ],
    [
        'q' => "4",
        'time' => "00:01",
        'id' => "quantity_4",
    ],
];

$status = 0;
$time = [];
foreach ($res as $item) {
    $st = intval($item['q']) - 1;
    if ($st == $status) {
        $time = $item;
    }
}
$data = explode(":", $time['time']);

echo json_encode($data);
