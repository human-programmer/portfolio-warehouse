<?php


namespace Templater\Pragma\Tests;


use Templater\Pragma\IDocLink;
use Templater\Pragma\IDocLinkToCreate;

class TestDocLinks extends \Templater\Pragma\DocLinks {
	static function saveFileLink(int $mainFileId, IDocLinkToCreate $link): IDocLink {
		return parent::saveFileLink($mainFileId, $link);
	}
}