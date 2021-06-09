<?php


namespace Services\Bitrix24;


interface iAccount {
	function getBitrix24MemberId(): string;
	function getBitrix24Referer(): string;
	function getBitrix24Lang(): string;
}