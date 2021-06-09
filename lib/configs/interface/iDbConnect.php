<?php


namespace Configs;


interface iDbConnect {
	function getDbName(): string;
	function getUser(): string;
	function getPassword(): string;
	function getHost(): string;
}