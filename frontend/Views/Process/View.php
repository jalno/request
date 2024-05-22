<?php
namespace themes\clipone\Views\Request\Process;
use \packages\base;
use \packages\base\Events;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\userpanel\Authorization;
use \packages\request\Views\Process\View as RequestView;
use \themes\clipone\ViewTrait;
use \themes\clipone\Navigation;
use \themes\clipone\Navigation\MenuItem;
use \themes\clipone\Views\Request\Box;
use \themes\clipone\Views\Request\Shortcut;
use \themes\clipone\request\Exceptions\TypeException;
class View extends RequestView{
	use ViewTrait;
	protected $process;
	protected $handler;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->handler = $this->getHandler();
		$this->setTitle([
			Translator::trans('requests'),
			Translator::trans('request.processView')
		]);
		Navigation::active("requests");
	}
	
}
