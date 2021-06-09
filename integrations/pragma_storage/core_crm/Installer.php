<?php


namespace PragmaStorage;


class Installer {

	public function __construct() {
	}

	static function install () : void {
		if(!self::issetStore())
			self::createDefaultStore();
	}

	static private function issetStore () : bool {
		return !!count(PragmaFactory::getStores()->getStores());
	}

	static private function createDefaultStore() : void {
		PragmaFactory::getStores()->createStore('Склад 1', '');
	}
}
