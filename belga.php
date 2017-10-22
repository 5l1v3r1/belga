<?php

require __DIR__ . '/vendor/autoload.php';

set_time_limit(0);

use Util\Network;
use Belga\Belga;

/**
 * Print usage
 * 
 * @param  array $argv args
 * @return string
 */
function usage($argv) {
	$usage =  'php %s -r ip-range -p port(s) ';
	$usage .= '[,-n needle] [,-t thread] [,-o output] [, --verbose] ' . PHP_EOL;
	$usage .= "
	-r           IP RANGE (192.168.0.1:192.168.0.255)
	-p           PORTS (80 OR 80,8080,...)
	-n           NEEDLE (\"Tomcat\")
	-t           THREADS (Default: 1)
	-o           OUTPUT (Default: output.txt)
	--verbose    VERBOSE MODE (Default: false)";

	return sprintf(
		$usage,
		$argv[0]
	);
}

/**
 * Get ips from ranges
 * 
 * @param  string $rangeNotation
 * @return \Generator
 */
function parseIps($rangeNotation) {
	$parsedIps = explode(':', $rangeNotation);
	return ['ipA' => $parsedIps[0], 'ipB' => $parsedIps[1]];
}

$opt = getopt('r:p:o:t:n:', ['verbose::']);

if (!isset($opt['r'], $opt['p'])) {
	exit (usage($argv));
}

$range = [$opt['r']];

// Generate ip list using generator if range given
if (strpos($range[0], ':') !== false) {
	$parsedIps = parseIps($range[0]);
	$range = Network::genIp($parsedIps['ipA'], $parsedIps['ipB']);
}

$ports = explode(',', $opt['p']);
$needle = isset($opt['n']) ? $opt['n'] : null;
$output = isset($opt['o']) ? $opt['o'] : 'output.txt';
$threads = isset($opt['t']) ? $opt['t'] : 1;
$verbose = array_key_exists('verbose', $opt) ? true : false;
$pool = new Pool($threads);

foreach ($range as $ip) {
	foreach ($ports as $port) {
		$pool->submit(
			new Belga(
				$ip,
				$port,
				$needle,
				$output,
				$verbose
			)
		);
	}
}

while ($pool->collect()) continue;
$pool->shutdown();




