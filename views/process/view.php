<?php
namespace packages\request\views\process;
use \packages\request\process;
use \packages\request\authorization;
class view extends \packages\request\view{
	protected $canEdit;
	protected $canLunch;
	function __construct(){
		$this->canEdit = authorization::is_accessed('edit');
		$this->canLunch = authorization::is_accessed('lunch');
	}
	public function setProcess(process $process){
		$this->setData($process, 'process');
	}
	protected function getProcess():process{
		return $this->getData('process');
	}
	public function setHandler($handler){
		$this->setData($handler, 'handler');
	}
	protected function getHandler(){
		return $this->getData('handler');
	}
}
