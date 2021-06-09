<?php


namespace PragmaStorage;


interface ICategoryStoreLinkStruct {
	function getStoreId(): int;
	function getCategoryId(): int;
	function getLinkStatus(): int;
}