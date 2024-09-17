<?php

namespace themes\clipone\Views\Request\Process;

use packages\base\Packages;
use packages\base\Translator;
use packages\base\View\Error;
use packages\request\Authorization;
use packages\request\Process;
use packages\request\Views\Process\Search as RequestList;
use packages\userpanel;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class Search extends RequestList
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;
    protected $multiuser = false;

    public function __beforeLoad()
    {
        $this->setTitle([
            t('requests'),
            t('requests.processList'),
        ]);
        $this->check_multiuser();
        $this->setButtons();
        Navigation::active('requests');
        if (empty($this->getProcessLists())) {
            $this->addNotFoundError();
        }
    }

    private function addNotFoundError()
    {
        $error = new Error();
        $error->setType(Error::NOTICE);
        $error->setCode('request.process.notfound');
        if (Packages::package('ticketing')) {
            $error->setData([
                [
                    'type' => 'btn-teal',
                    'txt' => t('ticketing.add'),
                    'link' => userpanel\url('ticketing/new'),
                ],
            ], 'btns');
        }
        $this->addError($error);
    }

    public function check_multiuser()
    {
        $this->multiuser = (bool) Authorization::childrenTypes();
    }

    public function setButtons()
    {
        $this->setButton('view', $this->canView, [
            'title' => t('request.processView'),
            'icon' => 'fa fa-bar-chart-o ',
            'classes' => ['btn', 'btn-xs', 'btn-success'],
        ]);
        $this->setButton('edit', $this->canEdit, [
            'title' => t('request.processEdit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-teal'],
        ]);
        $this->setButton('delete', $this->canDel, [
            'title' => t('request.processDelete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
        $this->setButton('lunch', $this->canLunch, [
            'title' => t('request.processLunch'),
            'icon' => 'fa fa-undo',
            'classes' => ['btn', 'btn-xs', 'btn-info'],
        ]);
    }

    protected function getStatusListForSelect(): array
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
        ];
    }

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            $item = new MenuItem('requests');
            $item->setTitle(t('requests'));
            $item->setURL(userpanel\url('requests'));
            $item->setIcon('fa fa-exclamation-circle');
            Navigation::addItem($item);
        }
    }

    public function getComparisonsForSelect()
    {
        return [
            [
                'title' => t('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => t('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => t('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }
}
