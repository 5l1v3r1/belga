<?php

/**
 * Belga - Just another php http banner grab
 *
 * Requirements:
 * - php >= 5.6 (php7 recommended) with thread safe
 * - php-pthreads
 * 
 * by @proclnas <proclnas@gmail.com>
 */

set_time_limit(0);

if (!extension_loaded('pthreads')) {
	exit('[-] Belga requires pthreads. Exiting...' . PHP_EOL);
} elseif (!is_dir('vendor')) {
	exit('[-] Use composer first to initialize Belga. Check the Readme to more info...' . PHP_EOL);
}

require __DIR__ . '/vendor/autoload.php';

use Util\Network;
use Belga\Belga;

$opt = getopt('r:p:o:t:n:', ['verbose::']);

if (!isset($opt['r'], $opt['p'])) {
	exit (Belga::usage());
}

$range = [$opt['r']];

// Generate ip list using generator if range given
if (strpos($range[0], ':') !== false) {
	$parsedIps = Belga::parseIps($range[0]);
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

while ($pool->collect(function() {})) continue;
$pool->shutdown();




