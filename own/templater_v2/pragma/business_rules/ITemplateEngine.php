<?php


namespace TemplateEngine\Pragma;


use FilesSystem\Pragma\IFile;
use Services\Amocrm\iAmoEntityParams;

interface ITemplateEngine {
	function createFile(IDocLinkToCreate $link, iAmoEntityParams $params): IFile;
	function createDir(string $title, int $parent_id): IFile;
}