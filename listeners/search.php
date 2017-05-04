<?php
namespace packages\request\listeners;
use \packages\base\db;
use \packages\base\translator;
use \packages\base\db\parenthesis;
use \packages\userpanel;
use \packages\request\authorization;
use \packages\userpanel\events\search as event;
use \packages\userpanel\search as saerchHandler;
use \packages\userpanel\search\link;
use \packages\request\process as request;
class search{
	public function find(event $e){
		if(authorization::is_accessed('search') and authorization::is_accessed('view')){
			$this->requests($e->word);
		}
	}
	public function requests(string $word){
		$types = authorization::childrenTypes();
		$request = new request();
		$parenthesis = new parenthesis();
		foreach(['title'] as $item){
			$parenthesis->where($item,$word, 'contains', 'OR');
		}
		db::join("userpanel_users", "userpanel_users.id=request_processes.user", "LEFT");
		if($types){
			db::where("userpanel_users.type", $types, 'in');
		}else{
			db::where("userpanel_users.id", authentication::getID());
		}
		foreach(['name','lastname','email','cellphone','phone'] as $item){
			$parenthesis->where("userpanel_users.{$item}", $word, 'contains', 'OR');
		}
		$request->where($parenthesis);
		foreach($request->get(null, "request_processes.*") as $request){
			$this->addRequest($request);
		}
	}
	private function addRequest(request $request){
		$statusTxt = '';
		switch($request->status){
			case(request::done):		$statusTxt = 'request.process.status.done';break;
			case(request::read):		$statusTxt = 'request.process.status.read';break;
			case(request::unread):		$statusTxt = 'request.process.status.unread';break;
			case(request::disagreement):$statusTxt = 'request.process.status.disagreement';break;
			case(request::running): 	$statusTxt = 'request.process.status.running';break;
			case(request::failed): 		$statusTxt = 'request.process.status.failed';break;
			case(request::cancel): 		$statusTxt = 'request.process.status.cancel';break;
		}
		$result = new link();
		$result->setLink(userpanel\url('requests/view/'.$request->id));
		$result->setTitle(translator::trans('request.search.title', [
			'title' => $request->title
		]));
		$result->setDescription(translator::trans("request.search.description", [
			'user' => $request->user->getFullName(),
			'status' => translator::trans($statusTxt)
		]));
		saerchHandler::addResult($result);
	}
}
