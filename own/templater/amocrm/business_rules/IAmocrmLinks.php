<?php


namespace Templater\Amocrm;


use Files\iFile;
use Services\Amocrm\iAmoEntityParams;
use Templater\Pragma\IDocLinks;
use Templater\Pragma\IDocLinkToCreate;

interface IAmocrmLinks extends IDocLinks {
	function createAndLink(IDocLinkToCreate $link, iAmoEntityParams $params): iFile;
}