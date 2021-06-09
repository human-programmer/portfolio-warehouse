<?php


namespace PragmaStorage;

require_once __DIR__ . '/ICategoryStoreLinkStruct.php';

interface ICategoryStoreLink extends ICategoryStoreLinkStruct {
	function getStore(): iStore;
	function getCategory(): iCategory;
}