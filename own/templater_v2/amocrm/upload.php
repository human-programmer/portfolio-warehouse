<?php

namespace TemplateEngine\Amocrm;

//header('Access-Control-Allow-Origin: *');

use FilesSystem\Pragma\FileStruct;
use const FilesSystem\TYPE_FILE_IS_FILE;

require_once __DIR__ . '/Factory.php';

//require_once __DIR__ . '/../../../lib/log/LogJSON.php';
//
//$logger = new \LogJSON('files', 'files');
//$logger->set_container('');
//$logger->add('$_REQUEST', $_REQUEST);
//$logger->add('$_FILES', $_FILES);

try {
	Factory::amocrmInitFromRequest('upload_template');
	$fileModel = $_FILES['file'];
	$fileModel['parent_id'] = getTargetParentId();
	$fileModel['type'] = TYPE_FILE_IS_FILE;
	$fileStruct = FileStruct::createFromRequest($fileModel);
	$file = Factory::getFiles()->createFromRequest($fileStruct, $fileModel['tmp_name']);
	echo json_encode($file->getExternalModel());
} catch (\Exception $e) {
	http_response_code(499);
	Factory::getLogWriter()->send_error($e);
}

function getTargetParentId(): int {
	return findParentId() ?? \TemplateEngine\Pragma\Factory::getTemplateDirs()->getTemplatesDirId();
}

function findParentId(): int|null {
	return $_REQUEST['parent_id'] ?? null;
}