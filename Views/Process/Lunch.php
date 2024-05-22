<?php

namespace packages\request\Views\Process;

use packages\request\Process;
use packages\request\Views\Form;

class Lunch extends Form
{
    public function setProcess(Process $process)
    {
        $this->setData($process, 'process');
    }

    protected function getProcess(): Process
    {
        return $this->getData('process');
    }
}
