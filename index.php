<?php

use App\Checker;
use App\SlackNotifier;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

require_once(__DIR__ . "/vendor/autoload.php");

set_time_limit(0);
date_default_timezone_set(config('timezone'));

try {
    (new Checker(
        new Client(config('client')),
        new CookieJar(),
        config('slack.hook') ? new SlackNotifier(config('slack.hook'), config('slack.settings')) : null
    ))->run();
} catch (Exception $e) {
    file_put_contents(__DIR__ . "/logs/" . date("Y-m-d_h-i-s") . ".log", $e);
}
