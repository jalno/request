<?php
namespace themes\clipone\views\request\process;
use \packages\base;
use \packages\base\events;
use \packages\base\translator;
use \packages\userpanel;
use \packages\request\process;
use \packages\request\views\process\delete as requestDelete;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation;
class delete extends requestDelete{
	use viewTrait, formTrait;
	protected $process;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->setTitle([
			translator::trans('requests'),
			translator::trans('request.processDelete')
		]);
		navigation::active("requests");
	}
}
