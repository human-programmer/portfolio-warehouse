<?php


namespace Generals;


use Configs\Configs;

trait Dashboard {
	function getDashboardApiKeysSchema() : string {
		return '`' . $this->getDashboardDb() . '`.`owners`';
	}

	function getDashboardDb () : string {
		return Configs::getDbNames()->getDashboard();
	}
}