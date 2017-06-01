<?php
namespace themes\clipone\views\request\process;
use \packages\base;
use \packages\base\events;
use \packages\base\translator;
use \packages\userpanel;
use \packages\request\process;
use \packages\request\views\process\edit as requestEdit;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation;
class edit extends requestEdit{
	use viewTrait, formTrait;
	protected $process;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->setTitle([
			translator::trans('requests'),
			translator::trans('request.processEdit')
		]);
		navigation::active("requests");
	}
	protected function getStatusForSelect():array{
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
			[
				'title' => translator::trans("request.process.status.inprogress"),
				'value' => process::inprogress
			]
		];
	}
}
