<?php
namespace packages\request\controllers;
use \packages\base\db;
use \packages\base\http;
use \packages\base\NotFound;
use \packages\base\view\error;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\inputValidation;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\request\events;
use \packages\request\process;
use \packages\request\controller;
use \packages\request\authorization;
use \packages\request\view;
use \packages\request\authentication;
use \packages\request\events\process as event;
class processes extends controller{
	protected $authentication = true;
	private function getProcess($data){
		$types = authorization::childrenTypes();
		$process = new process();
		$process->join(user::class, "user", 'INNER');
		if($types){
			$process->where("userpanel_users.type", $types, 'in');
		}else{
			$process->where("userpanel_users.id", authentication::getID());
		}
		$process->where('request_processes.id', $data['process']);
		$process = $process->getOne("request_processes.*");
		if(!$process){
			throw new NotFound;
		}
		return $process;
	}
	public function search(){
		authorization::haveOrFail('search');
		$view = view::byName("\\packages\\request\\views\\process\\search");
		$types = authorization::childrenTypes();
		$process = new process();
		$inputsRules = [
			'id' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true
			],
			'user' => [
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			],
			'title' => [
				'type' => 'string',
				'optional' =>true,
				'empty' => true
			],
			'status' => [
				'type' => 'number',
				'optional' => true,
				'empty' => true,
				'values' => [
					process::done,
					process::read,
					process::unread,
					process::disagreement,
					process::running,
					process::failed,
					process::cancel
				]
			],
			'word' => [
				'type' => 'string',
				'optional' => true,
				'empty' => true
			],
			'comparison' => [
				'values' => ['equals', 'startswith', 'contains'],
				'default' => 'contains',
				'optional' => true
			]
		];
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['user']) and $inputs['user']){
				$user = user::byId($inputs['user']);
				if(!$user){
					throw new inputValidation("user");
				}
			}
			foreach(['id', 'title', 'status', 'user'] as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, ['id', 'status', 'user'])){
						$comparison = 'equals';
					}
					$process->where("request_processes.".$item, $inputs[$item], $comparison);
					if(in_array($item, ['domain', 'user'])){
						$subService = true;
					}
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(['title'] as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("request_processes.".$item, $inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$process->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		db::join("userpanel_users", "userpanel_users.id=request_processes.user", "INNER");
		if($types){
			db::where("userpanel_users.type", $types, 'in');
		}else{
			db::where("userpanel_users.id", authentication::getID());
		}
		$process->orderBy('id', 'DESC');
		$process->pageLimit = $this->items_per_page;
		$processes = $process->paginate($this->page, 'request_processes.*');
		$view->setDataList($processes);
		$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function view($data){
		authorization::haveOrFail('view');
		$process = $this->getProcess($data);
		$view = view::byName("\\packages\\request\\views\\process\\view");
		$view->setProcess($process);
		$process->buildFrontend($view);
		$view->setHandler($process->getHandler());
		if($process->status == process::unread and authorization::is_accessed('lunch')){
			$process->status = process::read;
			$process->save();
		}
		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('edit');
		$process = $this->getProcess($data);
		$view = view::byName("\\packages\\request\\views\\process\\edit");
		$view->setProcess($process);
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
				if(isset($inputs['status'])){
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
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('delete');
		$process = $this->getProcess($data);
		$view = view::byName("\\packages\\request\\views\\process\\delete");
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
		authorization::haveOrFail('lunch');
		$process = $this->getProcess($data);
		if(in_array($process->status, [process::done, process::running])){
			throw new NotFound();
		}
		$view = view::byName("\\packages\\request\\views\\process\\lunch");
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
