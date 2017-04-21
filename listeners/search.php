<?php
namespace packages\request\listeners;
use \packages\base\db;
use \packages\userpanel;
use \packages\request\authorization;
use \packages\userpanel\events\search as event;
use \packages\userpanel\search as saerchHandler;
use \packages\userpanel\search\link;
class search{
	public function find(event $e){
		if(authorization::is_accessed('search') and authorization::is_accessed('view')){
			$this->services($e->word);
		}
	}
}
