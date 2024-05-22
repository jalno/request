<?php
namespace themes\clipone\Views\Request\Process;
use \packages\base;
use \packages\base\Events;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\request\Process;
use \packages\request\Views\Process\Edit as RequestEdit;
use \themes\clipone\ViewTrait;
use \themes\clipone\Views\FormTrait;
use \themes\clipone\Navigation;
class Edit extends RequestEdit{
	use ViewTrait, FormTrait;
	protected $process;
	public function __beforeLoad(){
		$this->process = $this->getProcess();
		$this->setTitle([
			Translator::trans('requests'),
			Translator::trans('request.processEdit')
		]);
		Navigation::active("requests");
	}
	protected function getStatusForSelect():array{
		return [
			[
				'title' => Translator::trans("choose"),
				'value' => ''
			],
			[
				'title' => Translator::trans("request.process.status.done"),
				'value' => Process::done
			],
			[
				'title' => Translator::trans("request.process.status.read"),
				'value' => Process::read
			],
			[
				'title' => Translator::trans("request.process.status.unread"),
				'value' => Process::unread
			],
			[
				'title' => Translator::trans("request.process.status.disagreement"),
				'value' => Process::disagreement
			],
			[
				'title' => Translator::trans("request.process.status.running"),
				'value' => Process::running
			],
			[
				'title' => Translator::trans("request.process.status.failed"),
				'value' => Process::failed
			],
			[
				'title' => Translator::trans("request.process.status.cancel"),
				'value' => Process::cancel
			],
			[
				'title' => Translator::trans("request.process.status.inprogress"),
				'value' => Process::inprogress
			]
		];
	}
}
