<?php
namespace packages\request\controllers;

use packages\base\{Process as BaseProcess, db, view\Error, views\FormError, http, InputValidation, NotFound, db\Parenthesis};
use packages\request\{Authentication, Authorization, Controller, Events, events\Process as Event, Process, View, Views};
use packages\userpanel;
use packages\userpanel\{User};

class Processes extends Controller {
	private static function getProcess($data): Process {
		$me = Authentication::getID();
		$types = Authorization::childrenTypes();
		$model = new Process();
		$model->with("user");
		if ($types) {
			$model->where("userpanel_users.type", $types, "IN");
		} else {
			$model->where("request_processes.user", $me);
		}
		$model->where("request_processes.id", $data["process"]);
		$process = $model->getOne();
		if (!$process) {
			throw new NotFound();
		}
		return $process;
	}

	protected $authentication = true;

	public function search() {
		Authorization::haveOrFail("search");
		$view = View::byName(views\process\Search::class);
		$this->response->setView($view);
		$me = Authentication::getID();
		$types = Authorization::childrenTypes();
		$inputs = $this->checkinputs(array(
			"id" => [
				"type" => "number",
				"optional" => true,
			],
			"user" => [
				"type" => User::class,
				"optional" => true,
				"query" => function($query) use ($types, $me) {
					if ($types) {
						$query->where("type", $types, "IN");
					} else {
						$query->where("id", $me);
					}
				}
			],
			"title" => [
				"type" => "string",
				"optional" => true,
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
				"type" => "string",
				"values" => ["equals", "startswith", "contains"],
				"default" => "contains",
				"optional" => true
			]
		));
		$model = new Process();
		$model->with("user");
		foreach (["id", "title", "status", "user"] as $item) {
			if (!isset($inputs[$item])) {
				continue;
			}
			$comparison = $inputs["comparison"];
			if ($item != "title") {
				$comparison = "equals";
				if ($item == "user") {
					$inputs[$item] = $inputs[$item]->id;
				}
			}
			$model->where("request_processes." . $item, $inputs[$item], $comparison);
		}
		if (isset($inputs["word"]) and !isset($inputs["title"])) {
			$model->where("request_processes.title", $inputs["word"], $inputs["comparison"]);
		}
		if ($types) {
			$model->where("userpanel_users.type", $types, "IN");
		} else {
			$model->where("request_processes.user", $me);
		}
		$model->orderBy("request_processes.id", "DESC");
		$model->pageLimit = $this->items_per_page;
		$processes = $model->paginate($this->page);
		$view->setDataList($processes);
		$view->setPaginate($this->page, $model->totalCount, $this->items_per_page);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function view($data) {
		Authorization::haveOrFail("view");
		$process = self::getProcess($data);
		$view = View::byName(views\process\View::class);
		$this->response->setView($view);
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
		Authorization::haveOrFail("edit");
		$process = self::getProcess($data);
		$view = View::byName(views\process\Edit::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function update($data) {
		Authorization::haveOrFail("edit");
		$process = self::getProcess($data);
		$view = View::byName(views\process\Edit::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$inputsRules = [
			"title" => [
				"type" => "string",
				"optional" => true
			],
			"status" => [
				"type" => "number",
				"optional" => true,
				"values" => Process::STATUSES,
			],
			"note" => [
				"type" => "string",
				"optional" => true,
				"empty" => true
			]
		];
		$inputs = $this->checkinputs($inputsRules);
		if (isset($inputs["title"]) and $inputs["title"]) {
			$process->title = $inputs["title"];
		}
		if (isset($inputs["note"])) {
			if ($inputs["note"]) {
				$process->setParam("note", $inputs["note"]);
			} else {
				$process->deleteParam("note");
			}
		}
		if (isset($inputs["status"]) and $inputs["status"] != $process->status) {
			$process->status = $inputs["status"];
			switch ($inputs["status"]) {
				case (Process::done):
					$event = new events\processes\complete\Done($process);
					$event->trigger();
					break;
				case (Process::inprogress):
					$event = new events\processes\InProgress($process);
					$event->trigger();
					break;
				case (Process::failed):
					$event = new events\processes\complete\Failed($process);
					$event->trigger();
					break;
			}
		}
		$process->save();
		$view->setDataForm($this->inputsvalue($inputsRules));
		$this->response->setStatus(true);
		return $this->response;
	}
	public function delete($data){
		Authorization::haveOrFail("delete");
		$process = self::getProcess($data);
		$view = View::byName(views\process\Delete::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function destroy($data) {
		Authorization::haveOrFail("delete");
		$process = self::getProcess($data);
		$view = View::byName(views\process\Delete::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$process->delete();
		$event = new events\processes\Delete($process);
		$event->trigger();
		$this->response->setStatus(true);
		$this->response->Go(userpanel\url("requests"));
		return $this->response;
	}
	public function lunch($data){
		Authorization::haveOrFail("lunch");
		$process = self::getProcess($data);
		if (in_array($process->status, [Process::done, Process::running])){
			throw new NotFound();
		}
		$view = View::byName(views\process\Lunch::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$this->response->setStatus(true);
		return $this->response;
	}
	public function do($data) {
		Authorization::haveOrFail("lunch");
		$process = self::getProcess($data);
		if (in_array($process->status, [Process::done, Process::running])){
			throw new NotFound();
		}
		$view = View::byName(views\process\Lunch::class);
		$view->setProcess($process);
		$this->response->setView($view);
		$process->runInBackground();
		$this->response->Go(userpanel\url("requests/view/" . $process->id));
		$this->response->setStatus(true);
		return $this->response;
	}
}
