<?php
namespace packages\request\views\process;
use \packages\request\authorization;
use \packages\request\views\listview;
use \packages\base\views\traits\form as formTrait;
class search extends listview{
	use formTrait;
	protected $canAdd;
	protected $canView;
	protected $canEdit;
	protected $canDel;
	protected $canLunch;
	static protected $navigation;
	function __construct(){
		$this->canView = authorization::is_accessed('view');
		$this->canAdd = authorization::is_accessed('add');
		$this->canEdit = authorization::is_accessed('edit');
		$this->canDel = authorization::is_accessed('delete');
		$this->canLunch = authorization::is_accessed('lunch');
	}
	public function getProcessLists(){
		return $this->dataList;
	}
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('search');
	}
}
