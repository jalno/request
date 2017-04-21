<?php
namespace packages\request;
use \packages\base\db;
use \packages\base\date;
use \packages\base\db\dbObject;
use \packages\base\process as baseprocess;
use \packages\request\view;
use \packages\request\process\param;
use \packages\request\exceptions\typeException;
class process extends dbObject{
	const done = 1;
	const read = 2;
	const unread = 3;
	const disagreement = 4;
	const running = 5;
	const failed = 6;
	const cancel = 7;
	const runner = 1;
	const process = 2;
	private $handler;
	protected $dbTable = "request_processes";
	protected $primaryKey = "id";
	protected $account;
	private $needModify = false;
	protected $dbFields = [
        'title' => ['type' => 'text', 'required' => true],
        'user' => ['type' => 'int', 'required' => true],
        'operator' => ['type' => 'int'],
        'create_at' => ['type' => 'int', 'required' => true],
        'done_at' => ['type' => 'int'],
        'type' => ['type' => 'text', 'required' => true],
        'parameters' => ['type' => 'text'],
		'status' => ['type' => 'int', 'required' => true]
	];
	protected $serializeFields = ['parameters'];
    protected $relations = [
		'user' => ['hasOne', 'packages\userpanel\user', 'user'],
		'operator' => ['hasOne', 'packages\userpanel\user', 'operator'],
		'params' => ['hasMany', 'packages\request\process\param', 'process']
	];
	protected $tmparams = [];
	protected function preLoad($data){
		if(!isset($data['status'])){
			$data['status'] = self::unread;
		}
		if(!isset($data['create_at'])){
			$data['create_at'] = date::time();
		}
		return $data;
	}
	public function setParam(string $name, $value){
		$param = false;
		foreach($this->params as $p){
			if($p->name == $name){
				$param = $p;
				break;
			}
		}
		if(!$param){
			$param = new param([
				'name' => $name,
				'value' => $value
			]);
		}else{
			$param->value = $value;
		}

		if(!$this->id){
			$this->tmparams[$name] = $param;
		}else{
			$param->process = $this->id;
			return $param->save();
		}
	}
	public function param(string $name){
		if(!$this->id){
			return(isset($this->tmparams[$name]) ? $this->tmparams[$name]->value : null);
		}else{
			foreach($this->params as $param){
				if($param->name == $name){
					return $param->value;
				}
			}
			return false;
		}
	}
	public function deleteParam(string $name):bool{
		if(!$this->id){
			if(isset($this->tmparams[$name])){
				unset($this->tmparams[$name]);
				return true;
			}
		}else{
			$param = new param();
			$param->where("service", $this->id);
			$param->where('name', $name);
			if($param->getOne()){
				return $param->delete();
			}
		}
		return false;
	}
	public function save($data = null){
		if(($return = parent::save($data))){
			foreach($this->tmparams as $param){
				$param->process = $this->id;
				$param->save();
			}
			$this->tmparams = [];
		}
		return $return;
	}
	public function getHandler(){
		if(!$this->handler){
			if(!class_exists($this->type)){
				throw new typeException(['type'=>$this->type]);
			}
			$this->handler = new $this->type($this);
		}
		return $this->handler;
	}
	public function buildFrontend(view $view){
		$this->getHandler()->buildFrontend($view);
	}
	public function runInBackground():baseprocess{
		$process = new baseprocess();
		$process->name = "packages\\request\\processes\\requests@runner";
		$process->parameters = array(
			'request' => $this->id
		);
		$process->save();
		$this->addProcess($process, self::runner);
		$process->background_run();
		return $process;
	}
	public function runAndWaitFor(int $timeoust = 0){
		$process = $this->runInBackground();
		return $process->waitFor($timeoust);
	}
	public function addProcess(baseprocess $process, int $type = self::process){
		db::insert('request_base', array(
			'request' => $this->id,
			'process' => $process->id,
			'type' => $type
		));
	}
}