<?php

namespace packages\request\Views\Process;

use packages\request\Authorization;
use packages\request\Process;

class View extends \packages\request\View
{
    protected $canEdit;
    protected $canLunch;

    public function __construct()
    {
        $this->canEdit = Authorization::is_accessed('edit');
        $this->canLunch = Authorization::is_accessed('lunch');
    }

    public function setProcess(Process $process)
    {
        $this->setData($process, 'process');
    }

    protected function getProcess(): Process
    {
        return $this->getData('process');
    }

    public function setHandler($handler)
    {
        $this->setData($handler, 'handler');
    }

    protected function getHandler()
    {
        return $this->getData('handler');
    }
}
