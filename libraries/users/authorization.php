<?php
namespace packages\request;
use \packages\userpanel\authorization as UserPanelAuthorization;
use \packages\userpanel\authentication;
class authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'request'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'request'){
		parent::haveOrFail($permission, $prefix);
	}
}
