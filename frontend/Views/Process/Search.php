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
            Translator::trans('requests'),
            Translator::trans('requests.processList'),
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
                    'txt' => Translator::trans('ticketing.add'),
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
            'title' => Translator::trans('request.processView'),
            'icon' => 'fa fa-bar-chart-o ',
            'classes' => ['btn', 'btn-xs', 'btn-success'],
        ]);
        $this->setButton('edit', $this->canEdit, [
            'title' => Translator::trans('request.processEdit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-teal'],
        ]);
        $this->setButton('delete', $this->canDel, [
            'title' => Translator::trans('request.processDelete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
        $this->setButton('lunch', $this->canLunch, [
            'title' => Translator::trans('request.processLunch'),
            'icon' => 'fa fa-undo',
            'classes' => ['btn', 'btn-xs', 'btn-info'],
        ]);
    }

    protected function getStatusListForSelect(): array
    {
        return [
            [
                'title' => Translator::trans('choose'),
                'value' => '',
            ],
            [
                'title' => Translator::trans('request.process.status.done'),
                'value' => Process::done,
            ],
            [
                'title' => Translator::trans('request.process.status.read'),
                'value' => Process::read,
            ],
            [
                'title' => Translator::trans('request.process.status.unread'),
                'value' => Process::unread,
            ],
            [
                'title' => Translator::trans('request.process.status.disagreement'),
                'value' => Process::disagreement,
            ],
            [
                'title' => Translator::trans('request.process.status.running'),
                'value' => Process::running,
            ],
            [
                'title' => Translator::trans('request.process.status.failed'),
                'value' => Process::failed,
            ],
            [
                'title' => Translator::trans('request.process.status.cancel'),
                'value' => Process::cancel,
            ],
        ];
    }

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            $item = new MenuItem('requests');
            $item->setTitle(Translator::trans('requests'));
            $item->setURL(userpanel\url('requests'));
            $item->setIcon('fa fa-exclamation-circle');
            Navigation::addItem($item);
        }
    }

    public function getComparisonsForSelect()
    {
        return [
            [
                'title' => Translator::trans('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => Translator::trans('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => Translator::trans('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }
}
