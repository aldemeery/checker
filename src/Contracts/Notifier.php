<?php

namespace App\Contracts;

interface Notifier
{
    /**
     * Send a notification.
     *
     * @param string $content Notification content.
     *
     * @return void
     */
    public function notify(string $content);
}
