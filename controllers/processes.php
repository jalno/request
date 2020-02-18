<?php
namespace packages\request\controllers;

use packages\base\{Process as BaseProcess, db, view\Error, views\FormError, http, InputValidation, NotFound, db\Parenthesis};
use packages\request\{Authentication, Authorization, Controller, Events, events\Process as Event, Process, View, Views};
use packages\userpanel;
use packages\userpanel\{User};

class Processes extends Controller {
	protected $authentication = true;
	private function getProcess($data){
		$types = Authorization::childrenTypes();
		$process = new Process();
		$process->join(User::class, "user", "INNER");
		if ($types) {
			$process->where("userpanel_users.type", $types, "IN");
		} else {
			$process->where("userpanel_users.id", Authentication::getID());
		}
		$process->where("request_processes.id", $data["process"]);
		$process = $process->getOne("request_processes.*");
		if (!$process) {
			throw new NotFound;
		}
		return $process;
	}
	public function search(){
		Authorization::haveOrFail("search");
		$view = View::byName(views\process\Search::class);
		$this->response->setView($view);
		$types = Authorization::childrenTypes();
		$myID = Authentication::getID();
		$inputsRules = [
			"id" => [
				"type" => "number",
				"optional" => true,
			],
			"user" => [
				"type" => User::class,
				"optional" =>true,
			],
			"title" => [
				"type" => "string",
				"optional" =>true,
			],
			"status" => [
				"type" => "number",
				"optional" => true,
				"values" => Process::STATUSES,
			],
			"word" => [
				"type" => "string",
				"optional" => true,
			],
			"comparison" => [
				"values" => ["equals", "startswith", "contains"],
				"default" => "contains",
				"optional" => true
			]
		];
		$inputs = $this->checkinputs($inputsRules);
		$process = new Process();
		foreach (["id", "title", "status", "user"] as $item) {
			if (isset($inputs[$item]) and $inputs[$item]) {
				$comparison = $inputs["comparison"];
				if (in_array($item, ["id", "status", "user"])) {
					$comparison = "equals";
				}
				if ($inputs[$item] instanceof db\dbObject) {
					$inputs[$item] = $inputs[$item]->id;
				}
				$process->where("request_processes." . $item, $inputs[$item], $comparison);
			}
		}
		if (isset($inputs["word"]) and $inputs["word"]) {
			$parenthesis = new Parenthesis();
			foreach(["title"] as $item){
				if (!isset($inputs[$item]) or !$inputs[$item]) {
					$parenthesis->where("request_processes." . $item, $inputs["word"], $inputs["comparison"], "OR");
				}
			}
			$process->where($parenthesis);
		}
		db::join("userpanel_users", "userpanel_users.id=request_processes.user", "INNER");
		if ($types) {
			$process->where("userpanel_users.type", $types, "IN");
		} else {
			$process->where("userpanel_users.id", $myID);
		}
		$process->orderBy("id", "DESC");
		$process->pageLimit = $this->items_per_page;
		$processes = $process->paginate($this->page, "request_processes.*");
		$view->setDataList($processes);
		$view->setDataForm($this->inputsvalue($inputsRules));
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function view($data) {
		Authorization::haveOrFail("view");
		$view = View::byName(views\process\View::class);
		$this->response->setView($view);
		$process = $this->getProcess($data);
		if ($process->status == Process::unread and Authorization::is_accessed("lunch")) {
			$process->status = Process::read;
			$process->save();
		}
		$view->setProcess($process);
		$process->buildFrontend($view);
		$view->setHandler($process->getHandler());
		// if request failed, get last process error and if error instance of packages\base\view\Error, add it to the view
		if ($process->status == Process::failed) {
			$lastBaseProcess = new BaseProcess();
			db::join("request_base", "request_base.process=base_processes.id", "INNER");
			$lastBaseProcess->where("request_base.request", $process->id);
			$lastBaseProcess->where("request_base.type", Process::process);
			$lastBaseProcess->joinWhere("base_processes.status", BaseProcess::error);
			$lastBaseProcess->orderBy("base_processes.id", "DESC");
			$lastBaseProcess = $lastBaseProcess->getOne("base_processes.response");
			if ($lastBaseProcess->response and $lastBaseProcess->response instanceof Error) {
				$view->addError($lastBaseProcess->response);
			}
		}
		$this->response->setStatus(true);
		return $this->response;
	}
	public function edit($data) {
		Authorization::haveOrFail('edit');
		$view = View::byName(views\process\Edit::class);
		$process = $this->getProcess($data);
		$view->setProcess($process);
		$this->response->setView($view);
		if(http::is_post()){
			$this->response->setStatus(false);
			$inputsRules = [
				'title' => [
					'type' => 'string',
					'optional' => true
				],
				'status' => [
					'type' => 'number',
					'optional' => true,
					'values' => [
						process::done,
						process::read,
						process::unread,
						process::disagreement,
						process::running,
						process::failed,
						process::cancel,
						process::inprogress
					]
				],
				'note' => [
					'type' => 'string',
					'optional' => true,
					'empty' => true
				]
			];
			try{
				$inputs = $this->checkinputs($inputsRules);
				if(isset($inputs['note'])){
					if(!$inputs['note']){
						unset($inputs['note']);
					}
				}
				foreach(['title', 'status'] as $item){
					if(isset($inputs[$item])){
					}
						$process->$item = $inputs[$item];
				}
				if(isset($inputs['note'])){
					$process->setParam('note', $inputs['note']);
				}
				if(isset($inputs['status']) and $inputs['status'] != $process->status){
					switch($inputs['status']){
						case(process::done):
							$event = new events\processes\complete\done($process);
							$event->trigger();
							break;
						case(process::inprogress):
							$event = new events\processes\inprogress($process);
							$event->trigger();
							break;
						case(process::failed):
							$event = new events\processes\complete\failed($process);
							$event->trigger();
							break;
					}
				}
				$process->save();
				$this->response->setStatus(true);
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('delete');
		$process = $this->getProcess($data);
		$view = view::byName(views\process\Delete::class);
		$view->setProcess($process);
		if(http::is_post()){
			$this->response->setStatus(false);
			try{
				$process->delete();
				$event = new events\processes\delete($process);
				$event->trigger();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('requests'));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function lunch($data){
		Authorization::haveOrFail('lunch');
		$process = $this->getProcess($data);
		if(in_array($process->status, [process::done, process::running])){
			throw new NotFound();
		}
		$view = view::byName(views\process\Lunch::class);
		$view->setProcess($process);
		if(http::is_post()){
			$this->response->setStatus(false);
			try{
				$process->runInBackground();
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('requests/view/'.$process->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
