<?php


namespace Templater\Amocrm\Events;

require_once __DIR__ . '/../Factory.php';

class Factory extends \Templater\Amocrm\Factory {
	protected static function getClientReferer(): string {
		$subdomain = $_REQUEST['account']['subdomain'];
		return $subdomain . '.amocrm.ru';
	}
}