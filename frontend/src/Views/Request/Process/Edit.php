<?php

namespace themes\clipone\Views\Request\Process;

use packages\base\Translator;
use packages\request\Process;
use packages\request\Views\Process\Edit as RequestEdit;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Edit extends RequestEdit
{
    use ViewTrait;
    use FormTrait;
    protected $process;

    public function __beforeLoad()
    {
        $this->process = $this->getProcess();
        $this->setTitle([
            t('requests'),
            t('request.processEdit'),
        ]);
        Navigation::active('requests');
    }

    protected function getStatusForSelect(): array
    {
        return [
            [
                'title' => t('choose'),
                'value' => '',
            ],
            [
                'title' => t('request.process.status.done'),
                'value' => Process::done,
            ],
            [
                'title' => t('request.process.status.read'),
                'value' => Process::read,
            ],
            [
                'title' => t('request.process.status.unread'),
                'value' => Process::unread,
            ],
            [
                'title' => t('request.process.status.disagreement'),
                'value' => Process::disagreement,
            ],
            [
                'title' => t('request.process.status.running'),
                'value' => Process::running,
            ],
            [
                'title' => t('request.process.status.failed'),
                'value' => Process::failed,
            ],
            [
                'title' => t('request.process.status.cancel'),
                'value' => Process::cancel,
            ],
            [
                'title' => t('request.process.status.inprogress'),
                'value' => Process::inprogress,
            ],
        ];
    }
}
