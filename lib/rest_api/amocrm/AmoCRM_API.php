<?

namespace RestApi\Amocrm;


use Services\Amocrm\iNode;

require_once __DIR__ . '/../../log/LogWriter.php';

class AmoCRM_API {
	private iNode $node;
	private \LogWriter $log_writer;

	function __construct(iNode $node, \LogWriter $log_writer = null) {
		$this->node = $node;
		if ($log_writer)$this->log_writer = $log_writer;
	}

	private function queryWithNextLinks(string $first_link) {
		do {
			$answer = $this->query($first_link, [], 'GET');
			$result[] = $answer;
			$first_link = $this->get_next_link($answer);
		} while ($first_link);
		return $result ?? [];
	}

	function query(string $url, array $params = [], string $method = 'GET') {
		$answer = $this->node->amocrmRestQuery($url, $method, $params);
		$this->errors($answer['info']['statusCode']);
		return $answer['body'];
	}

	protected function get_next_link($answer) {
		if (!isset($answer) || !is_array($answer))
			return null;
		$next_link = isset($answer['_links']['next']['href']) ? $answer['_links']['next']['href'] : null;
		if (!$next_link)
			return null;
		$arr = parse_url($next_link);
		return ($arr['path'] && $arr['query']) ? $arr['path'] . '?' . $arr['query'] : null;
	}

	function users($params, $method, $user_id = '') {
		return $this->query("/api/v4/users/$user_id", $params, $method);
	}

	function pipelines($params, $method, $pipeline_id = '') {
		$pipeline_id = $pipeline_id ? "/$pipeline_id" : '';

		return $this->query("/api/v4/leads/pipelines$pipeline_id", $params, $method);
	}

	function webhooks($params, $method) {
		return $this->query('/api/v4/webhooks', $params, $method);
	}

	function tasks($params, $method) {
		return $this->query('/api/v4/tasks', $params, $method);
	}

	function leads($params, $method) {
		return $this->query('/api/v4/leads', $params, $method);
	}

	function contacts($params, $method) {
		return $this->query('/api/v4/contacts', $params, $method);
	}

	function companies($params, $method) {
		return $this->query('/api/v4/companies', $params, $method);
	}

	function customers($params, $method) {
		return $this->query('/api/v4/customers', $params, $method);
	}

	function get_notes(string $entity_type, int $entity_id = null, int $note_id = null): array {
		$e_id = $entity_id ? "/$entity_id" : '';
		$n_id = $note_id ? "/$note_id" : '';
		return $this->query("/api/v4/$entity_type$e_id/notes$n_id", [], 'GET');
	}

	function send_notes(string $entity_type, array $notes, string $method): array {

		if ($method !== 'POST' && $method !== 'PATCH')
			throw new \Exception('Method parameter must be POST or PATCH');

		return $this->query("/api/v4/$entity_type/notes", $notes, $method);
	}

	function account() {
		return $this->query('/api/v4/account', [], 'GET');
	}

	function field(string $type, string $method, int $field_id, array $params = []) {
		switch ($type) {
			case 'leads':
			case 'contacts':
			case 'companies':
			case 'customers':
			case 'customers/segments':
				return $this->query("/api/v4/$type/custom_fields/$field_id", $params, $method);

			default:
				throw new \Exception("Недопустимое значение переменной \$type: $type");
		}
	}

	function fields(string $type, string $method, array $params = []) {
		switch ($type) {
			case 'leads':
			case 'contacts':
			case 'companies':
			case 'customers':
			case 'customers/segments':
				if ($method !== 'GET')
					return $this->query("/api/v4/$type/custom_fields", $params, $method);

				$link = "/api/v4/$type/custom_fields?" . http_build_query($params);
				$answers = $this->queryWithNextLinks($link);

				$result['_embedded']['custom_fields'] = [];
				foreach ($answers as $answer)
					$result['_embedded']['custom_fields'] = array_merge($result['_embedded']['custom_fields'], $answer['_embedded']['custom_fields'] ?? []);

				return $result ?? [];

			default:
				throw new \Exception("Недопустимое значение переменной \$type: $type");
		}
	}

	function field_groups(string $type, string $method, array $params = [], int $group_id = null) {
		$url = "/api/v4/$type/custom_fields/groups" . (is_null($group_id) ? '' : "/$group_id"); // Если будет оканчиваться на слэш и без id вернёт ошибку

		switch ($type) {
			case 'leads':
			case 'contacts':
			case 'companies':
			case 'customers':
				return $this->query($url, $params, $method);

			default:
				throw new \Exception("Недопустимое значение переменной \$type: $type");
		}
	}

	function link(string $entity_type, int $entity_id, array $links) {
		$url = "/api/v4/$entity_type/$entity_id/link";
		return $this->query($url, $links, 'POST');
	}

	function widgets(string $widget_code = ''): array {
		$url = $widget_code ? "/api/v4/widgets/$widget_code" : '/api/v4/widgets';
		return $this->query($url);
	}

	function add_unsorted_form(array $params) {
		return $this->query('/api/v4/leads/unsorted/forms', $params, 'POST');
	}

	function download(string $file_name) {
		$link = "https://$this->referer/download/$file_name";

		$curl = curl_init();

		curl_setopt_array($curl, [CURLOPT_URL => $link, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => "", CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 0, CURLOPT_FOLLOWLOCATION => true, CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => "GET", CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->access_token],]);

		$response = curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		$this->errors($code);

		return $response;
	}

	function errors(int $code, array $out = []) {
		$errors = [301 => 'Moved permanently', 400 => 'Bad request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not found', 500 => 'Internal server error', 502 => 'Bad gateway', 503 => 'Service unavailable',];

		if ($code === 401)
			$this->setDisable();

		if ($code > 299)
			throw new \Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', isset($out['validation-errors']) ? 777 : $code);
	}

	private function log(string $message, $params = null) {
		isset($this->log_writer) && $this->log_writer->add('AmoCRM_API - ' . $message, $params);
	}

	protected function getLogWriter(): \LogWriter|null {
		return $this->log_writer ?? null;
	}

	protected function setDisable(): void {
	}
}