<?php
namespace packages\request\processes;

use packages\request\{events, process as request};
use packages\base\{process, log, NotFound, date, Response, Error};

class requests extends process{
	public function runner($data): Response {
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
		if(!$run){
			throw new Error("Failed to run process");
		}
		$log->reply("Success");
		$log->info("mark request as running");
		$request->status = request::running;
		$request->save();
		$request->addProcess($run, request::process);
		$log->debug("waiting for stop");
		$time = date::time();
		$run->waitFor(0, false);
		$log->reply(date::time() - $time, "seconds");
		$log->debug("change request status, if needed");
		$response = new Response();
		switch($run->status){
			case(process::stopped):
				$request->status = request::done;
				$request->done_at = date::time();
				$log->debug("send complete done notification trigger");
				$event = new events\processes\complete\done($request);
				$event->trigger();
				$log->reply("Sent");
				$response->setStatus(true);
				break;
			case(process::error):
				$request->status = request::failed;
				$log->debug("send complete failed notification trigger");
				$event = new events\processes\complete\failed($request);
				$event->trigger();
				$log->reply("Sent");
				$response->setStatus(false);
				break;
		}
		$request->save();
		return $response;
	}
}