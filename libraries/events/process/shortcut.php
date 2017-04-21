<?php
namespace packages\request\events\process;
class shortcut{
	const green = 'green';
	const bricky = 'bricky';
	const teal = 'teal';
	const black = 'black';
	public $name;
	public $icon;
	public $color;
	public $title;
	public $text;
	public $link;
	public $priority = 0;
	public $size = 4;
	function __construct($name){
		$this->name = $name;
	}
	public function setLink($title, $link){
		$this->link = array($title, $link);
	}
}
