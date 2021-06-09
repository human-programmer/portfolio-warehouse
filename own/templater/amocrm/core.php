<?php

namespace Templater\Amocrm;

require_once __DIR__ . '/Factory.php';

try {
	\Templater\Amocrm\Factory::init('core');
	Factory::getLogWriter()->add('$_REQUEST', $_REQUEST);
	switch (getAction()){
		case 'update.doc':
			\Templater\Amocrm\Factory::getFilesFactory()->delete(getTargetFileId());
		case 'create.and.link.doc':
			$links = Factory::createAmoDicLinks();
			$linkedFile = $links->createAndLink(getDocLink(), getEntityParams());
			$answer = [$linkedFile->getExternalModel()];
			break;
		case 'get.links':
			$linkedFiles = Factory::createAmoDicLinks()->getLinksOfEntity(getEntityType(), getEntityId());
			$answer = fetchModels($linkedFiles);
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

function getDocLink(): \Templater\Pragma\IDocLinkToCreate {
	return new \Templater\Pragma\DocLink(getQuery()['doc_link']);
}

function getEntityParams(): \Services\Amocrm\iAmoEntityParams {
	return new AmoEntityParams(getQuery()['params']);
}

function getEntityType(): string {
	return getQuery()['entity_type'];
}

function getEntityId(): int {
	return getQuery()['entity_id'];
}

function getQuery(): array {
	return $_REQUEST['query'];
}

function fetchModels(array $linkedFiles): array {
	foreach ($linkedFiles as $linkedFile)
		$result[] = $linkedFile->getExternalModel();
	return $result ?? [];
}