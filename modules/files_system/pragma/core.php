<?php


namespace FilesSystem\Pragma;

require_once __DIR__ . '/Factory.php';

try {
	initFactory();
	switch (getAction()) {
		case 'remove.files':
			Factory::getFiles()->delete(getTargetFilesId());
			$result = true;
			break;
		default:
			throw new \Exception("Invalid action '" . getAction() . "'");
	}
	echo json_encode(['result' => $result]);
	exit;
} catch (\Exception $e) {
	$logger = Factory::issetLogger() ? Factory::getLogWriter() : new \LogJSON($_SERVER['HTTP_REFERER'], 'FilesSystemErrors');
	$logger->set_container('');
	$logger->send_error($e);
	http_response_code(499);
	echo 'ERROR: ' . $e->getMessage();
}

function initFactory(): void {
	Factory::initFromParams(getReferer(), getModuleCode());
}

function getReferer(): string {
	return parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ?? new \Exception("HTTP_REFERER not found");
}

function getModuleCode(): string {
	return $_REQUEST['code'] ?? throw new \Exception("Module code is missing");
}

function getAction(): string {
	return $_REQUEST['action'] ?? throw new \Exception("'action' is missing");
}

function getTargetFilesId(): array {
	if(!is_array($_REQUEST['files'])) throw new \Exception("'files' is missing");
	foreach ($_REQUEST['files'] as $index => $file)
		$id[] = (int) $file['id'] ?? throw new \Exception("invalid 'files[$index][id]' field");
	return $id;
}