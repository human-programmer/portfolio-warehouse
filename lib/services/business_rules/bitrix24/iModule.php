<?php


namespace Services\Bitrix24;


interface iModule {
	function setBitrix24SecretKey(string $key): void;
	function getBitrix24IntegrationId(): string;
	function setBitrix24IntegrationId(string $id): void;
}