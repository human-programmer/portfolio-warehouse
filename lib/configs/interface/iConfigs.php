<?php


namespace Configs;


interface iConfigs {
	static function getDbConnect(): iDbConnect;
	static function getDbNames(): iDbNames;
	static function getServices(): iServiceServer;
	static function isDev(): bool;
	static function isHosting(): bool;
	static function getCurrentDomain(): string;
}