<?php

namespace packages\request\Processes;

use packages\base\Date;
use packages\base\Log;
use packages\base\NotFound;
use packages\base\Process;
use packages\base\Response;
use packages\base\View\Error;
use packages\request\Events;
use packages\request\Process as Request;

class Requests extends Process
{
    public function runner($data): Response
    {
        Log::setLevel('debug');
        $log = Log::getInstance();
        $log->debug('check for request parameter');
        if (!isset($data['request'])) {
            throw new \Exception('need request parameter');
        }
        $log->reply('OK');
        $log->debug("Looking for request {$data['request']}");
        $request = new Request();
        $request->where('id', $data['request']);
        $request = $request->getOne();
        if (!$request) {
            throw new NotFound('notfound request');
        }
        $log->reply('Found');
        $log->info('running the process, if has it');
        $handler = $request->getHandler();
        $run = $handler->runProcess();
        if (!$run) {
            throw new Error('Failed to run process');
        }
        $log->reply('Success');
        $log->info('mark request as running');
        $request->status = Request::running;
        $request->save();
        $request->addProcess($run, Request::process);
        $log->debug('waiting for stop');
        $time = Date::time();
        $run->waitFor(0, false);
        $log->reply(Date::time() - $time, 'seconds');
        $log->debug('change request status, if needed');
        $response = new Response();
        switch ($run->status) {
            case Process::stopped:
                $request->status = Request::done;
                $request->done_at = Date::time();
                $log->debug('send complete done notification trigger');
                $event = new Events\Processes\Complete\Done($request);
                $event->trigger();
                $log->reply('Sent');
                $response->setStatus(true);
                break;
            case Process::error:
                $request->status = Request::failed;
                $log->debug('send complete failed notification trigger');
                $event = new Events\Processes\Complete\Failed($request);
                $event->trigger();
                $log->reply('Sent');
                $response->setStatus(false);
                break;
        }
        $request->save();

        return $response;
    }
}
