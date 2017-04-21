<?php
namespace packages\request\views\process;
use \packages\request\process;
use \packages\request\views\form;
class delete extends form{
	public function setProcess(process $process){
		$this->setData($process, 'process');
	}
	protected function getProcess():process{
		return $this->getData('process');
	}
}
