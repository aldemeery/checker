<?php

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        $config = require(__DIR__ . "/config.php");

        if (is_null($key)) {
            return $config;
        }

        if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        if (strpos($key, '.') === false) {
            return $config[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (array_key_exists($segment, $config)) {
                $config = $config[$segment];
            } else {
                return $default;
            }
        }

        return $config;
    }
}
