<?php

namespace Belga;

class Belga extends \Threaded {
	const VERSION = '1.0.0';
	const TIMEOUT = 5;
	const HEAD_VERB = 'HEAD / HTTP/1.1\r\n';
	const GET_VERB = 'GET / HTTP/1.1\r\n';

	/**
	 * Constructor 
	 * 
	 * @param string  $host    Host to connect
	 * @param string  $port    Port
	 * @param string  $output  File to save needles found
	 * @param string  $needle  String to search into response
	 * @param boolean $verbose Show more info
	 */
	public function __construct(
		$host,
		$port, 
		$output,
		$needle = null,
		$verbose = false
	) {
		$this->host = $host;
		$this->port = $port;
		$this->needle = $needle;
		$this->output = $output;
		$this->verbose = $verbose;
		$this->handle = false;
		$this->response = '';
	}

	/**
	 * Print usage
	 * 
	 * @param  array $argv args
	 * @return string
	 */
	public static function usage() {
		return  sprintf('php belga.php -r ip-range -p port(s) '.
        '[,-n needle] [,-t thread] [,-o output] [, --verbose] ' . PHP_EOL .
		'
		-r           IP RANGE (192.168.0.1:192.168.0.255)
		-p           PORTS (80 OR 80,8080,...)
		-n           NEEDLE "Tomcat", "apache", "http", etc...
		-t           THREADS (Default: 1)
		-o           OUTPUT (Default: output.txt)
		--verbose    VERBOSE MODE (Default: false)

		by @proclnas - v%s', self::VERSION);
	}

	/**
	 * Get ips from ranges
	 * 
	 * @param  string $rangeNotation
	 * @return \Generator
	 */
	public static function parseIps($rangeNotation) {
		$parsedIps = explode(':', $rangeNotation);
		return ['ipA' => $parsedIps[0], 'ipB' => $parsedIps[1]];
	}

	/**
	 * Get host header line
	 * 
	 * @return array
	 */
	private function getHostAndPort() {
		return [
			'Host' => sprintf('%s:%s', $this->host, $this->port)
		];
	}

	/**
	 * Get default payload
	 * @return array
	 */
	private function getDefaultPayload() {
		return array_merge(
			$this->getHostAndPort(),
			[
				'Connection' => 'close',
				'User-agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0'
			]
		);
	}

	/**
	 * Parge default or user payload
	 * 
	 * @return string
	 */
	private function parsePayload() {
		$payload = $this->getDefaultPayload();

		if ($this->payload !== null) {
			$payload = explode('\r\n', $this->payload);
			$payload = array_merge($this->getHostAndPort, $payload);
		}

		$keys = array_keys($payload);
		$counter = 0;

		return array_reduce($payload, function($parsedPayload, $current) use ($keys, $payload, &$counter){
			$parsedPayload .= sprintf('%s: %s\r\n', $keys[$counter], $current);
			if (($counter+1) == count($payload))
				$parsedPayload .= '\r\n';

			$counter++;
			return $parsedPayload;
		});
	}

	/**
	 * Connect to host
	 * 
	 * @return boolean
	 */
	private function connect() {
		if (!$this->handle = @fsockopen($this->host, $this->port, $errNo, $errStr, self::TIMEOUT))
			return false;

		return true;
	}

	/**
	 * Send request
	 * 
	 * @return void
	 */
	private function sendRequest() {
		fwrite(
			$this->handle, 
			sprintf(
				'%s%s',
				self::HEAD_VERB,
				$this->parsePayload()
			)
		);

		while (!feof($this->handle)) $this->response .= fgets($this->handle);
	}

	/**
	 * Show more info about request
	 * 
	 * @return void
	 */
	private function doVerbosity() {
		$this->sendRequest();

		echo sprintf('RESPONSE FROM: %s:%s' . PHP_EOL, $this->host, $this->port),
		PHP_EOL, 
		$this->response,
		PHP_EOL;
	}

	/**
	 * Check string in response
	 * 
	 * @return void
	 */
	private function checkNeedle() {
		// Get response only when a request was done previously
		if (!$this->response)
			$this->sendRequest();

		if(stripos($this->response, $this->needle) !== false) {
			$msg = sprintf(
				'[+] Needle "%s" found in %s:%s' . PHP_EOL, 
				$this->needle, 
				$this->host, 
				$this->port,
				$this->output
			);

			echo $msg;
			file_put_contents($this->output, $msg, FILE_APPEND | LOCK_EX);
		}
	}

	/**
	 * Main method
	 * 
	 * @return void
	 */
	private function knockKnock() {
		$msg = sprintf('[+] %s:%s' . PHP_EOL, $this->host, $this->port);

		if (!$this->connect()) {
			if ($this->verbose)
				echo sprintf('[-] %s:%s' . PHP_EOL, $this->host, $this->port);

			exit;
		}

		echo $msg;

		// Verbose
		if ($this->verbose) $this->doVerbosity();
		
		// Check needle in the response
		if ($this->needle) $this->checkNeedle();
	}

	/**
	 * Run thread | Inherit from \Threaded
	 * 
	 * @return void
	 */
	public function run() {
		$this->knockKnock();
	}
}