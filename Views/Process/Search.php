<?php
namespace packages\request\Views\Process;
use \packages\request\Authorization;
use \packages\request\Views\ListView;
use \packages\base\Views\Traits\Form as FormTrait;
class Search extends ListView{
	use FormTrait;
	protected $canAdd;
	protected $canView;
	protected $canEdit;
	protected $canDel;
	protected $canLunch;
	static protected $navigation;
	function __construct(){
		$this->canView = Authorization::is_accessed('view');
		$this->canAdd = Authorization::is_accessed('add');
		$this->canEdit = Authorization::is_accessed('edit');
		$this->canDel = Authorization::is_accessed('delete');
		$this->canLunch = Authorization::is_accessed('lunch');
	}
	public function getProcessLists(){
		return $this->dataList;
	}
	public static function onSourceLoad(){
		self::$navigation = Authorization::is_accessed('search');
	}
}
