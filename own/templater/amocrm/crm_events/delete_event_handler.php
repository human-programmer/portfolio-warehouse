<?php


namespace Templater\Amocrm\Events;

require_once __DIR__ . '/Factory.php';

try {
	Factory::init('delete_event_handler');
	Factory::getLogWriter()->add('$_REQUEST', $_REQUEST);
	$leadsIds = getLeadsIdToDelete();
	Factory::getLogWriter()->add('$leadsIds', $leadsIds);

	if(!count($leadsIds)) die;

	$filesToDelete = getFilesToDelete($leadsIds);
	Factory::getLogWriter()->add('$filesToDelete', $filesToDelete);
	deleteFiles($filesToDelete);

} catch (\Exception $e) {
	Factory::getLogWriter()->send_error($e);
}

function getLeadsIdToDelete(): array {
	$leads = $_REQUEST['leads']['delete'];
	foreach ($leads as $lead)
		$ids[] = (int) $lead['id'];
	return $ids ?? [];
}

function getFilesToDelete(array $leadIds): array {
	$links = Factory::createAmoDicLinks();
	$result = [];
	foreach ($leadIds as $lead_id)
		$result = array_merge($result, $links->getLinksOfEntity('leads', $lead_id));
	return $result;
}

function deleteFiles(array $files): void {
	foreach ($files as $file)
		Factory::getFilesFactory()->delete($file->getId());
}