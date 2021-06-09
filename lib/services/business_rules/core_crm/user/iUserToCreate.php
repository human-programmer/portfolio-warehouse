<?php


namespace Services\Pragma;


interface iUserToCreate {
	function getName(): string|null;
	function getSurname(): string|null;
	function getMiddleName(): string|null;
	function getEmail(): string|null;
	function getPhone(): string|null;
	function getLang(): string|null;
}