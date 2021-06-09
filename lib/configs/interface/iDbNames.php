<?php


namespace Configs;


interface iDbNames {
	function getAmocrmInterface(): string;
	function getBitrix24Interface(): string;
	function getDashboard(): string;
	function getCalculator(): string;
	function getCoreCrm(): string;
	function getModules(): string;
	function getUsers(): string;
	function getStorage(): string;
	function getAdditionalStorage(): string;
	function getMarket(): string;
	function getFiles(): string;
	function getModulesSettings(): string;
}