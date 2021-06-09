<?php

namespace FilesSystem\Pragma;

require_once __DIR__ . '/Factory.php';

//только для запуска тестов
//AccountVariables::setRootDorForTest("C:/Os/OSPanel/domains/smart-dev.core_crm/api/modules/files_system/tests/core_crm/data_sets/files");

try {
	$file_id = $_REQUEST['file'];
	$token = $_REQUEST['token'];
	Files::sendFile($file_id, $token);
	exit;
} catch (\Exception $e) {
	http_response_code(499);
	echo 'ERROR: ' . $e->getMessage();
}