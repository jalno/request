<?php

namespace packages\request\Events\Processes;

use packages\base\Event;
use packages\notifications\Notifiable;
use packages\request\Process;

class Delete extends Event implements Notifiable
{
    private $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public static function getName(): string
    {
        return 'request_process_delete';
    }

    public static function getParameters(): array
    {
        return [Process::class];
    }

    public function getArguments(): array
    {
        return [
            'process' => $this->getProcess(),
        ];
    }

    public function getTargetUsers(): array
    {
        return [$this->process->user];
    }
}
