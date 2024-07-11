<?php

namespace packages\request\Views\Process;

use packages\request\Process;
use packages\request\Views\Form;

class Edit extends Form
{
    public function setProcess(Process $process)
    {
        $this->setData($process, 'process');
        $this->setDataForm($process->toArray());
        $this->setDataForm($process->param('note'), 'note');
    }

    protected function getProcess(): Process
    {
        return $this->getData('process');
    }
}
