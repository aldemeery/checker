<?php

namespace App;

use App\Contracts\Checker as CheckerInterface;
use App\Contracts\Notifier;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Cookie\CookieJarInterface;

class Checker implements CheckerInterface
{
    /**
     * @var \GuzzleHttp\ClientInterface Http client instance.
     */
    protected $client;

    /**
     * @var \GuzzleHttp\Cookie\CookieJarInterface Cookie jar.
     */
    private $jar;

    /**
     * @var \App\Contracts\Notifier|null Notifier instance.
     */
    private $notifier;

    /**
     * Next action, "check-in" or "check-out".
     *
     * @var string
     */
    protected $next = "check-in";

    /**
     * Constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client Http client instance.
     * @param \GuzzleHttp\Cookie\CookieJarInterface $jar Cookie jar.
     * @param \App\Contracts\Notifier|null $notifier Notifier instance.
     */
    public function __construct(ClientInterface $client, CookieJarInterface $jar, ?Notifier $notifier = null)
    {
        $this->client = $client;
        $this->jar = $jar;
        $this->notifier = $notifier;
    }

    /**
     * Get the underlying http client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Log in to hr.my.
     *
     * @return void
     */
    public function login()
    {
        $response = json_decode((string) $this->client->post('user/account/login/employee', [
            'cookies' => $this->jar,
            'form_params' => [
                'email' => config('email'),
                'password' => config('password'),
            ]
        ])->getBody());

        echo $response->msg . PHP_EOL;
    }

    /**
     * Check-in or check-out.
     *
     * @return void
     */
    public function checkInOrOut()
    {
        static $actions = [0 => 1, 1 => 0];

        $extraData = json_decode((string) $this->client->get('employee/mvc/attendance/clock/last', [
            'cookies' => $this->jar,
        ])->getBody())->extraData;


        $response = json_decode((string) $this->client->post('employee/mvc/attendance/clock', [
            'cookies' => $this->jar,
            'form_params' => [
                'action' => (string) $actions[$extraData->lastAction ?? 1],
                'forDate' => (string) 0,
            ],
        ])->getBody());

        echo $response->msg . PHP_EOL;
    }

    /**
     * Run the checker.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = $this->getNextTimestamp();
        $msg = "Next action at: " . date("Y-m-d: h:i:s", $timestamp) . PHP_EOL;
        echo $msg;

        if ($this->notifier) {
            $this->notifier->notify(config('email') . ": " . $msg);
        }

        time_sleep_until($timestamp);

        if (!$this->inHoliday()) {
            $this->login();
            $this->checkInOrOut();
        }

        $this->updateNext();

        if ($this->notifier) {
            $this->notifier->notify(config('email') . ": " . "An action has been taken.");
        }

        $this->run();
    }

    /**
     * Get next action.
     *
     * @return string
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Update the next action.
     *
     * @return void
     */
    protected function updateNext()
    {
        switch ($this->next) {
            case 'check-in':
                $this->next = 'check-out';
                break;
            case 'check-out':
                $this->next = 'check-in';
                break;
            default:
                $this->next = 'check-in';
                break;
        }
    }

    /**
     * Get the next action timestamp.
     *
     * @return int
     */
    private function getNextTimestamp()
    {
        switch (config('mode')) {
            case 'psycho':
                return $this->psychoTime();
                break;
            case 'normal':
                return $this->normalTime();
                break;
            case 'asshole':
                return $this->assholeTime();
                break;
            default:
                return $this->normalTime();
                break;
        }
    }

    /**
     * Get the time for psycho employees, who show up every day
     * exactly at the same second.
     * (+0 seconds over configured times).
     *
     * @return int
     */
    private function psychoTime()
    {
        $nextTime = $this->getNextTime();

        return strtotime($nextTime);
    }

    /**
     * Get the time for normal employees.
     * (+1 to +15 minutes over configured times).
     *
     * @return int
     */
    private function normalTime()
    {
        $nextTime = $this->getNextTime();

        $extra = rand(1, 15);

        return strtotime("+{$extra} minutes", strtotime($nextTime));
    }

    /**
     * Get the time for asshole employees.
     * (+1 to +4 hours over configured times).
     *
     * @return int
     */
    private function assholeTime()
    {
        $nextTime = $this->getNextTime();

        $extra = rand(1, 4);

        return strtotime("+{$extra} hours", strtotime($nextTime));
    }

    /**
     * Get the next time string.
     *
     * @return string
     */
    private function getNextTime()
    {
        $nextTime = config($this->getNext());

        if ($this->hasPassed($nextTime)) {
            $nextTime = "tomorrow " . $nextTime;
        }

        return $nextTime;
    }

    /**
     * Determine whether the next time has passed.
     *
     * @param string $timeString Time string of the next time.
     *
     * @return boolean
     */
    private function hasPassed($timeString)
    {
        return time() - strtotime($timeString) > 0;
    }

    /**
     * Determine if today is a holiday.
     *
     * @return boolean
     */
    private function inHoliday()
    {
        $day = strtolower(date('l'));
        $date = date('Y-m-d');

        foreach ((config('holidays') ?? []) as $holiday) {
            $holiday = strtolower($holiday);

            if ($holiday == $day || $holiday == $date) {
                return true;
            }
        }

        return false;
    }
}
