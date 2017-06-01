<?php
namespace packages\request\listeners;
use \packages\notifications\events;
use \packages\request\events as requestEevents;
class notifications{
	public function events(events $events){
		$events->add(requestEevents\processes\inprogress::class);
		$events->add(requestEevents\processes\complete\done::class);
	}
}