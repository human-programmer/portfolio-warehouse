<?php

namespace PragmaStorage;

require_once __DIR__ . '/lib/Factory.php';
require_once __DIR__ . '/../core_crm/PragmaFactory.php';

$data = $_REQUEST;
$log_writer = new \LogJSON($data['referer'], WIDGET_NAME, 'install');
Factory::init(WIDGET_NAME, $data['referer'], $log_writer);

try {
	PragmaFactory::getInstaller()::install();
} catch (\Exception $e) {
	$log_writer->send_error($e);
}