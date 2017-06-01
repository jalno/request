<?php
namespace packages\request\events\processes;
use \packages\base\event;
use \packages\userpanel\user;
use \packages\notifications\notifiable;
use \packages\request\process;
class complete extends event implements notifiable{
	private $process;
	public function __construct(process $process){
		$this->process = $process;
	}
	public function getProcess():process{
		return $this->process;
	}
	public static function getName():string{
		return 'request_process_complete';
	}
	public static function getParameters():array{
		return [process::class];
	}
	public function getArguments():array{
		return [
			'process' => $this->getProcess()
		];
	}
	public function getTargetUsers():array{
		return [$this->process->user];
	}
}