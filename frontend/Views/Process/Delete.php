<?php

namespace themes\clipone\Views\Request\Process;

use packages\base\Translator;
use packages\request\Views\Process\Delete as RequestDelete;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Delete extends RequestDelete
{
    use ViewTrait;
    use FormTrait;
    protected $process;

    public function __beforeLoad()
    {
        $this->process = $this->getProcess();
        $this->setTitle([
            t('requests'),
            t('request.processDelete'),
        ]);
        Navigation::active('requests');
    }
}
