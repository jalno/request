<?php

namespace packages\request\Listeners;

use packages\notifications\Events;
use packages\request\Events as RequestEvents;

class Notifications
{
    public function events(Events $events)
    {
        $events->add(RequestEvents\Processes\InProgress::class);
        $events->add(RequestEvents\Processes\Complete\Done::class);
        $events->add(RequestEvents\Processes\Complete\Failed::class);
        $events->add(RequestEvents\Processes\Delete::class);
    }
}
