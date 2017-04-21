<?php
namespace packages\request\events\process;
class box{
	public $name;
	public $icon;
	public $priority = 0;
	public $html = '';
	public $size = 12;
	function __construct($name){
		$this->name = $name;
	}
	public function setHTML($html){
		$this->html = $html;
	}
	public function getHTML(){
		return $this->html;
	}
}
