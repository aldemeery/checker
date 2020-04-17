<?php

namespace App\Contracts;

use GuzzleHttp\ClientInterface;

interface Checker
{
    /**
     * Get the underlying http client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient();

    /**
     * Log in to hr.my.
     *
     * @return void
     */
    public function login();

    /**
     * Check-in or check-out.
     *
     * @return void
     */
    public function checkInOrOut();

    /**
     * Run the checker.
     *
     * @return void
     */
    public function run();

    /**
     * Get next action.
     *
     * @return string
     */
    public function getNext();
}
