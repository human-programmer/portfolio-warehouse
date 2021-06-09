<?php
require_once __DIR__ . '/LogWriter.php';

class LogJSON implements LogWriter {
	private $log_arr = [];
	private $container_name = null;
	private $prefix;
	private $dir_name;
	private $start_time;
	private bool $fatal_error = false;
	private bool $exception = false;

	public function __construct(string $referer, string $widget_name, string $prefix = '') {
		$dir = __DIR__ . '/../../..';
		$this->start_time = microtime(true);
		$this->dir_name = "$dir/logs/$referer/$widget_name/";
		$this->prefix = $prefix ? $prefix . '_' : '';
		register_shutdown_function($this->get_error_callback());
	}

	public function add(string $message, $params = null) {
		if ($this->container_name)
			$this->log_arr[$this->container_name][self::get_time() . "[$message]"] = $params;
		else
			$this->log_arr[self::get_time() . "[$message]"] = $params;
	}

	public function set_container(string $container_name) {
		$this->container_name = self::get_time() . " $container_name";
	}

	public function send_error(Exception $e, string $message = null) {
		$this->exception = true;
		$this->add($message ?? 'EXCEPTION', ['message' => $e->getMessage(), 'code' => $e->getCode(), 'line' => $e->getLine(), 'trace' => $e->getTrace(), 'file' => $e->getFile(),]);
	}

	private function get_error_callback(): callable {
		$log_writer = &$this;

		return function () use (&$log_writer) {

			$e = error_get_last() ?? json_last_error();

			if (!is_array($e))
				return;

			$e['message'] = explode('#', (string)$e['message'] ?? '');

			switch ($e['type']) {
				case E_COMPILE_ERROR:
				case E_ERROR:
				case E_CORE_ERROR:
				case E_RECOVERABLE_ERROR:
					$this->fatal_error = true;
					$log_writer->add('FATAL_ERROR', $e);
					break;
				default:
					if ($e)
						$log_writer->add('WARNING', $e);
			}
		};
	}

	static private function create_dir($name) {
		$info = php_uname();

		$_dir_name = $name;

		if (preg_match('/Windows/i', $info))
			$_dir_name = str_replace('/', "\\", $name);

		return mkdir($_dir_name, 0777, true);
	}

	static private function get_time() {
		$date = new DateTime();
		return $date->format('H:i:s:u');
	}

	static private function get_date() {
		return date('d.m.Y');
	}

	private function add_duration() {
		if ($this->start_time)
			$this->add('DURATION(sec)', microtime(true) - $this->start_time);
	}

	public function save_log() {
		$this->set_container('');
		if (!is_dir($this->dir_name))
			self::create_dir($this->dir_name);
		$file = fopen($this->getFullFileName(), 'c');
		$this->add_duration();
		$str = json_encode($this->log_arr);

		if (file_exists($this->getFullFileName()) && filesize($this->getFullFileName())) {
			$str = ',' . substr($str, 1);
			fseek($file, -1, SEEK_END);
		}

		fwrite($file, $str);
		fclose($file);
		$this->start_time = time();
		$this->log_arr = [];
	}

	private function getFullFileName() : string {
		return $this->dir_name . $this->getFileName();
	}

	private function getFileName() : string {
		if($this->fatal_error)
			return $this->getFatalErrorFileName();
		if($this->exception)
			return $this->getExceptionFileName();
		return $this->getName();
	}

	private function getFatalErrorFileName() : string {
		return "FATAL_ERROR_" . $this->getName();
	}

	private function getExceptionFileName() : string {
		return "EXCEPTION_" . $this->getName();
	}

	private function getName(): string {
		return "$this->prefix" . self::get_date() . '.json';
	}

	public function __destruct() {
		$this->save_log();
	}

	public function get_log(): array {
		return $this->log_arr;
	}

	function setPrefix(string $prefix): void {
		$this->prefix = $prefix;
	}
}