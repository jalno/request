<?php

namespace packages\request;

use packages\base\Date;
use packages\base\DB;
use packages\base\DB\DBObject;
use packages\base\Process as BaseProcess;
use packages\request\Exceptions\TypeException;
use packages\request\Process\Param;
use packages\userpanel\User;
use packages\request\Processes;

class Process extends DBObject
{
    public const done = 1;
    public const read = 2;
    public const unread = 3;
    public const disagreement = 4;
    public const running = 5;
    public const failed = 6;
    public const cancel = 7;
    public const inprogress = 8;
    public const STATUSES = [
        self::done,
        self::read,
        self::unread,
        self::disagreement,
        self::running,
        self::failed,
        self::cancel,
        self::inprogress,
    ];
    public const runner = 1;
    public const process = 2;
    private $handler;
    protected $dbTable = 'request_processes';
    protected $primaryKey = 'id';
    private $lastProcess;
    protected $dbFields = [
        'title' => ['type' => 'text', 'required' => true],
        'user' => ['type' => 'int', 'required' => true],
        'operator' => ['type' => 'int'],
        'create_at' => ['type' => 'int', 'required' => true],
        'done_at' => ['type' => 'int'],
        'type' => ['type' => 'text', 'required' => true],
        'parameters' => ['type' => 'text'],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $serializeFields = ['parameters'];
    protected $relations = [
        'user' => ['hasOne', User::class, 'user'],
        'operator' => ['hasOne', User::class, 'operator'],
        'params' => ['hasMany', Param::class, 'process'],
    ];
    protected $tmparams = [];

    protected function preLoad($data)
    {
        if (!isset($data['status'])) {
            $data['status'] = self::unread;
        }
        if (!isset($data['create_at'])) {
            $data['create_at'] = Date::time();
        }
        parent::$recursivelySerialize = true;

        return $data;
    }

    public function setParam(string $name, $value)
    {
        $param = false;
        foreach ($this->params as $p) {
            if ($p->name == $name) {
                $param = $p;
                break;
            }
        }
        if (!$param) {
            $param = new Param([
                'name' => $name,
                'value' => $value,
            ]);
        } else {
            $param->value = $value;
        }

        if (!$this->id) {
            $this->tmparams[$name] = $param;
        } else {
            $param->process = $this->id;

            return $param->save();
        }
    }

    public function param(string $name)
    {
        if (!$this->id) {
            return isset($this->tmparams[$name]) ? $this->tmparams[$name]->value : null;
        } else {
            foreach ($this->params as $param) {
                if ($param->name == $name) {
                    return $param->value;
                }
            }

            return false;
        }
    }

    public function deleteParam(string $name): bool
    {
        if (!$this->id) {
            if (isset($this->tmparams[$name])) {
                unset($this->tmparams[$name]);

                return true;
            }
        } else {
            $param = new Param();
            $param->where('process', $this->id);
            $param->where('name', $name);
            $param = $param->getOne();
            if ($param) {
                return $param->delete();
            }
        }

        return false;
    }

    public function save($data = null)
    {
        if ($return = parent::save($data)) {
            foreach ($this->tmparams as $param) {
                $param->process = $this->id;
                $param->save();
            }
            $this->tmparams = [];
        }
        parent::$recursivelySerialize = false;

        return $return;
    }

    public function getHandler()
    {
        if (!$this->handler) {
            if (!class_exists($this->type)) {
                throw new TypeException(['type' => $this->type]);
            }
            $this->handler = new $this->type($this);
        }

        return $this->handler;
    }

    public function buildFrontend(View $view)
    {
        $this->getHandler()->buildFrontend($view);
    }

    public function runInBackground(int $timeout = 0): BaseProcess
    {
        DB::where('request', $this->id);
        DB::where('type', self::process);
        $allbaseProcesses = DB::getValue('request_base', 'request_base.process', null);
        if (!is_array($allbaseProcesses)) {
            $allbaseProcesses = [];
        }
        $process = new BaseProcess();
        $process->name = Processes\Requests::class . '@runner';
        $process->parameters = [
            'request' => $this->id,
        ];
        $process->save();
        $this->addProcess($process, self::runner);
        $process->background_run();
        $time = time();
        while ((0 == $timeout or time() - $time < $timeout) and $process->isRunning()) {
            DB::where('request', $this->id);
            DB::where('type', self::process);
            $newbaseProcesses = DB::getValue('request_base', 'request_base.process', null);
            if (!is_array($newbaseProcesses)) {
                $newbaseProcesses = [];
            }
            if ($diff = array_values(array_diff($newbaseProcesses, $allbaseProcesses))) {
                $this->lastProcess = new BaseProcess();
                $this->lastProcess = $this->lastProcess->byId($diff[0]);
                break;
            }
            usleep(250000);
        }

        return $process;
    }

    public function runAndWaitFor(int $timeout = 0, bool $throwable = true)
    {
        $runner = $this->runInBackground($timeout);
        $running = $runner->waitFor($timeout, $throwable);
        if (BaseProcess::error == $runner->status) {
            $lastProcess = $this->getLastProcess();
            if ($throwable and $lastProcess->response instanceof \Exception) {
                throw $lastProcess->response;
            }
        }

        return $running;
    }

    public function getLastProcess()
    {
        if (!$this->lastProcess) {
            DB::join('request_base', 'base_processes.id=request_base.process', 'INNER');
            $this->lastProcess = new BaseProcess();
            $this->lastProcess->where('request_base.request', $this->id);
            $this->lastProcess->where('request_base.type', self::process);
            $this->lastProcess->orderBy('base_processes.start', 'DESC');
            $this->lastProcess = $this->lastProcess->getOne('base_processes.*');
        } else {
            $this->lastProcess->where('id', $this->lastProcess->id);
            $this->lastProcess = $this->lastProcess->getOne();
        }

        return $this->lastProcess;
    }

    public function addProcess(BaseProcess $process, int $type = self::process)
    {
        DB::insert('request_base', [
            'request' => $this->id,
            'process' => $process->id,
            'type' => $type,
        ]);
    }

    public function __get($key)
    {
        if ('response' == $key) {
            if ($lastProcess = $this->getLastProcess()) {
                return $lastProcess->response;
            } else {
                return null;
            }
        }

        return parent::__get($key);
    }
}
