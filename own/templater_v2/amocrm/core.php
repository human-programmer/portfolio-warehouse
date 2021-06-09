<?php

namespace TemplateEngine\Amocrm;

use TemplateEngine\Pragma\DocLink;
use TemplateEngine\Pragma\IDocLinkToCreate;

require_once __DIR__ . '/Factory.php';

try {
	Factory::amocrmInitFromRequest('core');
	switch (getAction()){
		case 'update.doc':
			Factory::getFiles()->delete(getTargetFileId());
		case 'create.and.link.doc':
			$file = Factory::getTemplateEngine()->createFile(getDocLink(), getEntityParams());
			$answer = [$file->getExternalModel()];
			break;
		case 'get.templates':
			$dir_id = Factory::getTemplateDirs()->getTemplatesDirId();
		case 'get.links':
		case 'get.card.files':
			$dir_id = $dir_id ?? Factory::getTemplateDirs()->getCardDirId(getEntityId(), getEntityType());
			$answer = Factory::getFiles()->getDirContentModels($dir_id);
			$answer[] = Factory::getFiles()->getFile($dir_id)->getExternalModel();
			break;
		case 'create.templates.dir':
			$parent_id = findParentId() ?? Factory::getTemplateDirs()->getTemplatesDirId();
			$file = Factory::getFiles()->createDir(getDirTitle(), $parent_id);
			$answer[] = $file->getExternalModel();
			break;
		default:
			throw new \Exception('Invalid action "' . getAction() . '"');
	}
	echo json_encode($answer);
} catch (\Exception $exception) {
	http_response_code(555);
	Factory::getLogWriter()->send_error($exception);
	echo 'Error';
}

function getAction(): string {
	return $_REQUEST['action'];
}

function getTargetFileId(): int {
	$id = (int) getQuery()['target_file_id'];
	$id || throw new \Exception('Invalid file id');
	return $id;
}

function getDocLink(): IDocLinkToCreate {
	return new DocLink([
		'entity_id' => getEntityId(),
		'entity_type' => getEntityType(),
		'template_id' => getTemplateId(),
	]);
}

function getEntityParams(): \Services\Amocrm\iAmoEntityParams {
	return new AmoEntityParams(getQuery()['params']);
}

function findParentId(): int|null {
	return getQuery()['parent_id'];
}

function getDirTitle(): string {
	return getQuery()['title'] ?? 'New folder';
}

function getTemplateId(): int {
	return getQuery()['template_id'] ?? throw new \Exception("template_id is missing");
}

function getEntityId(): int {
	return getQuery()['entity_id'] ?? throw new \Exception("entity_id is missing");
}

function getEntityType(): string {
	return getQuery()['entity_type'] ?? throw new \Exception("entity_type is missing");
}

function getQuery(): array {
	return $_REQUEST['query'];
}