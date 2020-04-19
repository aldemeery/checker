<?php

namespace App;

use App\Contracts\Notifier;
use Maknz\Slack\Client;

class SlackNotifier extends Client implements Notifier
{
    /**
     * Send a notification.
     *
     * @param string $content Notification content.
     *
     * @return void
     */
    public function notify(string $content)
    {
        $this->send($content);
    }
}
