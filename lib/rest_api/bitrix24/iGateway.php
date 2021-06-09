<?php


namespace RestApi\Bitrix24;


interface iGateway {
	function query(string $method, array $params = []);
}