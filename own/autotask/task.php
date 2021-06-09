<?php
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/LogTask.php';

$logger = LogTask::create_log("AutoTask", "run");

require_once __DIR__ . '/../autocall/amocrm/Controller/Hook.php';

$POST = $_POST;
$tasks = $POST['data'];
$parse_data = json_decode($tasks, true);
LogTask::LOG('Post', $parse_data);
try {
    if (count($parse_data) > 0) {
        foreach ($parse_data as $task) {
            $subdomain = $task['subdomain'];
            $element_id = $task['element_id'];
            $text = $task['text'];
            $task_id = $task['id'];
            $element_type = intval(($task['element_type']));
            LogTask::LOG('$element_type', $element_type);

            $element_id == 0 ? \Autocall\Amocrm\Hook::AutoTask_ZERO_init($subdomain, $text, $task_id, $logger) :
                \Autocall\Amocrm\Hook::AutoTaskInit($subdomain, $element_type, $element_id, $task_id, $logger);
        };
    }
} catch (\Exception $exception) {
    $logger->send_error($exception);
}
