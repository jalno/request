<?php
namespace packages\request\events;
use \packages\base\view;
use \packages\base\event;
use \packages\request\process as request;
use \packages\request\events\process\box;
use \packages\request\events\process\shortcut;
abstract class process extends event{
	public $request;
	public static $shortcuts = [];
	public static $boxs = [];
	public function __construct(request $request){
		$this->request = $request;
	}

	abstract public function runProcess();
	abstract public function buildFrontend(view $view);

	public function cancel():bool{}
	
	public static function addShortcut(shortcut $shortcut){
		foreach(self::$shortcuts as $key => $item){
			if($item->name == $shortcut->name){
				self::$shortcuts[$key] = $shortcut;
				return;
			}
		}
		self::$shortcuts[] = $shortcut;
	}
	public static function addBox(box $box){
		self::$boxs[] = $box;
	}
	public function getBoxs(){
		return self::$boxs;
	}
	public function generateShortcuts(){
		$rows = [];
		$lastrow = 0;
		$shortcuts = array_slice(self::$shortcuts, 0, max(3, floor(count(self::$shortcuts)/2)));
		foreach($shortcuts as $box){
			$rows[$lastrow][] = $box;
			$size = 0;
			foreach($rows[$lastrow] as $rowbox){
				$size += $rowbox->size;
			}
			if($size >= 12){
				$lastrow++;
			}
		}
		$html = '';
		foreach($rows as $row){
			$html .= "<div class=\"row\">";
			foreach($row as $shortcut){
				$html .= "<div class=\"col-sm-{$shortcut->size}\">";
				$html .= "<div class=\"core-box\">";
				$html .= "<div class=\"heading\">";
				$html .= "<i class=\"{$shortcut->icon} circle-icon circle-{$shortcut->color}\"></i>";
				$html .= "<h2>{$shortcut->title}</h2>";
				$html .= "</div>";
				$html .= "<div class=\"content\">{$shortcut->text}</div>";
				$html .= "<a class=\"view-more\" href=\"".$shortcut->link[1]."\"><i class=\"clip-arrow-left-2\"></i> ".$shortcut->link[0]."</a>";
				$html .= "</div>";
				$html .= "</div>";
			}
			$html .= "</div>";
		}
		return $html;
	}
	public function generateRows(){
		$rows = [];
		$lastrow = 0;
		foreach(self::$boxs as $box){
			$rows[$lastrow][] = $box;
			$size = 0;
			foreach($rows[$lastrow] as $rowbox){
				$size += $rowbox->size;
			}
			if($size >= 12){
				$lastrow++;
			}
		}
		$html = '';
		foreach($rows as $row){
			$html .= "<div class=\"row\">";
			foreach($row as $box){
				$html .= "<div class=\"col-sm-{$box->size}\">".$box->getHTML()."</div>";
			}
			$html .= "</div>";
		}
		return $html;
	}
}
