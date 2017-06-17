<?php
namespace packages\request\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'search',
			'view',
			'delete',
			'edit',
			'lunch'
		);
		foreach($permissions as $permission){
			permissions::add('request_'.$permission);
		}
	}
}
