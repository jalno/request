<?php
namespace packages\request\Events\Processes;
use \packages\base\Event;
use \packages\userpanel\User;
use \packages\notifications\Notifiable;
use \packages\request\Process;
class Complete extends Event implements Notifiable{
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