<?php


namespace Templater\Pragma;

require_once __DIR__ . '/IDocLinkToCreate.php';

interface IDocLink extends IDocLinkToCreate {
	function getFileId(): int;
}