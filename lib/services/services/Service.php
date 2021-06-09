<?php


namespace Services;

use Configs\Configs;

require_once __DIR__ . '/../Factory.php';

class Service {
	protected function __construct() {
		$this->clientModuleCode = Factory::getClientModuleCode();
		$this->accountReferer = Factory::getAccountReferer();
	}

	protected function servicesRequest(string $route, array $query = []): array {
		$request = $this->createRequest($query);
		$port = Configs::getServices()->getPort();
		$host = Configs::getServices()->getHost();
//		$host = '185.152.139.30';
		return self::postRequest($request, $port, $host, $route);
	}

	protected function modulesRequest(string $route, array $query = [] , array|null $headers = []): mixed {
		$request = $this->createRequest($query);
		$port = Configs::getServices()->getModulesPort();
		$host = Configs::getServices()->getHost();
//		$host = '185.152.139.30';
		return self::postRequest($request, $port, $host, $route);
	}

	protected function postRequest(array $request, int $port, string $host, string $route, array|null $headers = null): mixed {
		$path = "http://$host:$port$route";

		Factory::getLogWriter()->add('Service REquest', [$route, $request]);
		$test = json_encode($request);

		$headers = $headers ?? ['Content-Type: application/json'];

		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_HEADER => 0,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $path,
			CURLOPT_POSTFIELDS => json_encode($request)
		]);
		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		curl_close($curl);
		$fromJson = json_decode($result, true);
		$result = $fromJson ? $fromJson : $result;

		self::validator($result, $code);

		return $result ?? [];
	}

	private static function validator(mixed $answer, int $code): void {
		if((int) $code === 0) {
			throw new \Exception('Service is disabled');
		} else if((int) $code >= 400) {
			$message = $answer['error']['message'] ?? 'Service ERROR';
			Factory::getLogWriter()->add($message, $answer);
			throw new \Exception($message);
		} else if(!$answer) {
			Factory::getLogWriter()->add('Service ERROR', $answer);
			throw new \Exception('Service ERROR');
		}
	}

	static function createDefaultQuery(string $client_module_code = null, string $referer = null): array {
		return [
			'client_module_code' => $client_module_code ?? self::getClientModuleCode(),
			'account_referer' => $referer ?? self::getAccountReferer()
		];
	}

	private function createRequest(array $query): array {
		$defaultQuery = self::createDefaultQuery();
		return array_merge($defaultQuery, $query);
	}

	protected static function createQueryWithFilter(array $filter): array {
		return ['filter' => $filter];
	}

	protected static function createQueryFromData(array $data): array {
		return array_merge(self::createDefaultQuery(), self::createQueryWithData($data));
	}

	protected static function createQueryWithData(array $data): array {
		return ['data' => $data];
	}

	protected static function getClientModuleCode(): string {
		return Factory::getClientModuleCode();
	}

	protected static function getAccountReferer(): string {
		return Factory::getAccountReferer();
	}
}