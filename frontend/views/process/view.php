<?php
namespace themes\clipone\views\request\process;
use \packages\base;
use \packages\base\events;
use \packages\base\translator;
use \packages\userpanel;
use \packages\userpanel\authorization;
use \packages\request\views\process\view as requestView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\request\box;
use \themes\clipone\views\request\shortcut;
use \themes\clipone\request\exceptions\typeException;
class view extends requestView{
	use viewTrait;
	protected $process;
	protected $handler;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->handler = $this->getHandler();
		$this->setTitle([
			translator::trans('requests'),
			translator::trans('request.processView')
		]);
		navigation::active("requests");
	}
	
}
