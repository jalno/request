<?php

namespace themes\clipone\Views\Request\Process;

use packages\base\Translator;
use packages\request\Views\Process\View as RequestView;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class View extends RequestView
{
    use ViewTrait;
    protected $process;
    protected $handler;

    public function __beforeLoad()
    {
        $this->process = $this->getProcess();
        $this->handler = $this->getHandler();
        $this->setTitle([
            Translator::trans('requests'),
            Translator::trans('request.processView'),
        ]);
        Navigation::active('requests');
    }
}
