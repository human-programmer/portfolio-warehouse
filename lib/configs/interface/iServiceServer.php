<?php


namespace Configs;


interface iServiceServer {
	function getPort(): int;
	function getModulesPort(): int;
	function getHost(): string;
}