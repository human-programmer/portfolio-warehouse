<?php


namespace Configs;

require_once __DIR__ . '/../interface/iServiceServer.php';

class ServiceServer implements iServiceServer {
	private int $port;
	private int $modules_port;
	private string $host;

	function __construct(array $model) {
		$this->port = $model['port'];
		$this->modules_port = $model['modules_port'];
		$this->host = $model['host'];
	}

	function getPort(): int {
		return $this->port;
	}

	function getHost(): string {
		return $this->host;
	}

	function getModulesPort(): int {
		return $this->modules_port;
	}
}