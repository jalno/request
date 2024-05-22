<?php
namespace packages\request;
use \packages\userpanel\Authorization as UserPanelAuthorization;
use \packages\userpanel\Authentication;
class Authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'request'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'request'){
		parent::haveOrFail($permission, $prefix);
	}
}
