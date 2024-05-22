<?php

namespace packages\request\Events\Processes\Complete;

use packages\request\Events\Processes\Complete;

class Done extends Complete
{
    public static function getName(): string
    {
        return 'request_process_done';
    }
}
