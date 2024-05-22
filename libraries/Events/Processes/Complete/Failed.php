<?php
namespace packages\request\Events\Processes\Complete;
use \packages\request\Events\Processes\Complete;
class Failed extends Complete{
	public static function getName():string{
		return 'request_process_failed';
	}
}