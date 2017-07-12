<?php
namespace packages\request\views\process;
use \packages\request\process;
use \packages\request\views\form;
class edit extends form{
	public function setProcess(process $process){
		$this->setData($process, 'process');
		$this->setDataForm($process->toArray());
		$this->setDataForm($process->param('note'), 'note');
	}
	protected function getProcess():process{
		return $this->getData('process');
	}
}
