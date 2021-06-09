<?php

namespace Templater\Amocrm;


use const Templater\WIDGET_NAME;

require_once __DIR__ . '/../Factory.php';
require_once __DIR__ . '/modules/AmoEntityParams.php';
require_once __DIR__ . '/modules/AmoDocLinks.php';

class Factory extends \Templater\Factory {
	static function init(string $logPrefix): void {
		parent::init($logPrefix);
		self::initNode();
		\Files\Factory::init(self::$node, self::getLogWriter());
	}

	protected static function initNode(): void {
		$node = \Services\Factory::getNodesService()->findAmocrmReferer(WIDGET_NAME, static::getClientReferer());
		$node || throw new \Exception('Node not found');
		parent::$node = $node;
	}

	static function createAmoDicLinks(): IAmocrmLinks {
		return new AmoDocLinks(self::getNode()->getAccount()->getPragmaAccountId());
	}
}