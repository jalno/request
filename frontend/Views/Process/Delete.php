<?php
namespace themes\clipone\Views\Request\Process;
use \packages\base;
use \packages\base\Events;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\request\Process;
use \packages\request\Views\Process\Delete as RequestDelete;
use \themes\clipone\ViewTrait;
use \themes\clipone\Views\FormTrait;
use \themes\clipone\Navigation;
class Delete extends RequestDelete{
	use ViewTrait, FormTrait;
	protected $process;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->setTitle([
			Translator::trans('requests'),
			Translator::trans('request.processDelete')
		]);
		Navigation::active("requests");
	}
}
