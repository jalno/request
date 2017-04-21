<?php
namespace packages\request\processes;
use \packages\base\process;
use \packages\base\log;
use \packages\base\NotFound;
use \packages\base\date;
use \packages\request\process as request;
class requests extends process{
	public function runner($data){
		log::setLevel('debug');
		$log = log::getInstance();
		$log->debug("check for request parameter");
		if(!isset($data['request'])){
			throw new \Exception("need request parameter");
		}
		$log->reply("OK");
		$log->debug("Looking for request {$data['request']}");
		$request = new request();
		$request->where("id", $data['request']);
		$request = $request->getOne();
		if(!$request){
			throw new NotFound("notfound request");
		}
		$log->reply("Found");
		$log->info("running the process, if has it");
		$handler = $request->getHandler();
		$run = $handler->runProcess();
		if($run){
			$log->reply("Success");
			$log->info("mark request as running");
			$request->status = request::running;
			$request->save();
			$request->addProcess($run, request::process);
			$log->debug("waiting for stop");
			$time = date::time();
			$run->waitFor();
			$log->reply(date::time() - $time, "seconds");
			$log->debug("change request status, if needed");
			switch($run->status){
				case(process::stopped):
					$request->status = request::done;
					$request->done_at = date::time();
					break;
				case(process::error):
					$request->status = request::failed;
					break;
			}
			$request->save();
			
		}

	}
}