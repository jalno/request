<?php
namespace packages\request\events\processes\complete;
use \packages\request\events\processes\complete;
class done extends complete{
	public static function getName():string{
		return 'request_process_done';
	}
}