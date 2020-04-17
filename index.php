<?php

use App\Checker;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

require_once(__DIR__ . "/vendor/autoload.php");

date_default_timezone_set(config('timezone'));
$checker = new Checker(new Client(config('client')), new CookieJar());

try {
    $checker->run();
} catch (Exception $e) {
    file_put_contents(__DIR__ . "/logs/" . date("Y-m-d_h-i-s") . ".log", $e);
}
