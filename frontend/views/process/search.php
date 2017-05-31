<?php
namespace themes\clipone\views\request\process;
use \packages\base\packages;
use \packages\base\view\error;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\request\authorization;
use \packages\request\process;
use \packages\request\views\process\search as requestList;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;
class search extends requestList{
	use viewTrait, listTrait, formTrait;
	protected $multiuser = false;
	function __beforeLoad(){
		$this->setTitle([
			translator::trans('requests'),
			translator::trans('requests.processList')
		]);
		$this->addAssets();
		$this->check_multiuser();
		$this->setButtons();
		navigation::active("requests");
		if(empty($this->getProcessLists())){
			$this->addNotFoundError();
		}
	}
	private function addNotFoundError(){
		$error = new error();
		$error->setType(error::NOTICE);
		$error->setCode('request.process.notfound');
		if(packages::package('ticketing')){
			$error->setData([
				[
					'type' => 'btn-teal',
					'txt' => translator::trans('ticketing.add'),
					'link' => userpanel\url('ticketing/new')
				]
			], 'btns');
		}
		$this->addError($error);
	}
	private function addAssets(){
		$this->addJSFile(theme::url('assets/js/pages/process.js'));
	}
	public function check_multiuser(){
		$this->multiuser = (bool)authorization::childrenTypes();
	}
	public function setButtons(){
		$this->setButton('view', $this->canView, [
			'title' => translator::trans('request.processView'),
			'icon' => 'fa fa-bar-chart-o ',
			'classes' => ['btn', 'btn-xs', 'btn-success']
		]);
		$this->setButton('edit', $this->canEdit, [
			'title' => translator::trans('request.processEdit'),
			'icon' => 'fa fa-edit',
			'classes' => ['btn', 'btn-xs', 'btn-teal']
		]);
		$this->setButton('delete', $this->canDel, [
			'title' => translator::trans('request.processDelete'),
			'icon' => 'fa fa-times',
			'classes' => ['btn', 'btn-xs', 'btn-bricky']
		]);
		$this->setButton('lunch', $this->canLunch, [
			'title' => translator::trans('request.processLunch'),
			'icon' => 'fa fa-undo',
			'classes' => ['btn', 'btn-xs', 'btn-info']
		]);
	}
	protected function getStatusListForSelect():array{
		return [
			[
				'title' => translator::trans("choose"),
				'value' => ''
			],
			[
				'title' => translator::trans("request.process.status.done"),
				'value' => process::done
			],
			[
				'title' => translator::trans("request.process.status.read"),
				'value' => process::read
			],
			[
				'title' => translator::trans("request.process.status.unread"),
				'value' => process::unread
			],
			[
				'title' => translator::trans("request.process.status.disagreement"),
				'value' => process::disagreement
			],
			[
				'title' => translator::trans("request.process.status.running"),
				'value' => process::running
			],
			[
				'title' => translator::trans("request.process.status.failed"),
				'value' => process::failed
			],
			[
				'title' => translator::trans("request.process.status.cancel"),
				'value' => process::cancel
			],
		];
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$item = new menuItem("requests");
			$item->setTitle(translator::trans('requests'));
			$item->setURL(userpanel\url('requests'));
			$item->setIcon('fa fa-exclamation-circle');
			navigation::addItem($item);
		}
	}
	public function getComparisonsForSelect(){
		return [
			[
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			],
			[
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			],
			[
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			]
		];
	}
}
