<?php


namespace Services\Amocrm;


interface iAmoEntityParams {
	function getEntities(): array;
	function getManagers(): array;
	function getCustomFields(): array;
}