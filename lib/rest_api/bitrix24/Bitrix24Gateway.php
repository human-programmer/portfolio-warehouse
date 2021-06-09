<?php


namespace RestApi\Bitrix24;


use Generals\General\Modules\iAccountsModule;

require_once __DIR__ . '/iGateway.php';
require_once __DIR__ . '/CONSTANTS.php';

class Bitrix24Gateway implements iGateway {
	static private \LogWriter|null $log_writer = null;

	public function __construct(private iAccountsModule $accounts_module) {
	}

	static function setLogWriter(\LogWriter $logWriter) : void {
		self::$log_writer = $logWriter;
	}

	function query(string $method, array $params = []){
		$fullUrl = $this->getFullUrl($method, $params);

		$curl = curl_init();
		curl_setopt_array($curl, [CURLOPT_HEADER => 0, CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $fullUrl]);
		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		curl_close($curl);

		$result = json_decode($result, 1);
		self::checkResult($result, $code);

		return $result;
	}

	private function checkResult($result, int $code) : void {
		$code >= 400 && self::throwException($result);
	}

	static private function throwException ($result_answer) : int {
		switch ($result_answer['error']) {
			case 'invalid_token':
				throw new \Exception("Invalid token", INVALID_TOKEN);
			case 'insufficient_scope':
				throw new \Exception("Not enough rights", NOT_ENOUGH_RIGHTS);
			default:
				throw new \Exception("Unknown error", UNKNOWN_ERROR);
		}
	}

	private function getFullUrl(string $method, array $params) : string {
		return $this->accounts_module->getBitrix24ClientEndPoint() . "$method?" . http_build_query($params) . '&auth=' . $this->accounts_module->getBitrix24AccessToken();
	}

	static private function log(string $message, $params = null): void {
		self::$log_writer?->add("Bitrix24Gateway - $message", $params);
	}
}